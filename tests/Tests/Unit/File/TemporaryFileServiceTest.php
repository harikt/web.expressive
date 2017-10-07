<?php

namespace Dms\Web\Expressive\Tests\Unit\File\Persistence;

use Dms\Common\Structure\DateTime\DateTime;
use Dms\Common\Structure\FileSystem\File;
use Dms\Common\Testing\CmsTestCase;
use Dms\Core\Model\EntityNotFoundException;
use Dms\Core\Persistence\ArrayRepository;
use Dms\Core\Util\IClock;
use Dms\Web\Expressive\File\Persistence\ITemporaryFileRepository;
use Dms\Web\Expressive\File\TemporaryFile;
use Dms\Web\Expressive\File\TemporaryFileService;
use Illuminate\Config\Repository;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TemporaryFileServiceTest extends CmsTestCase
{
    /**
     * @var ITemporaryFileRepository
     */
    protected $tempFileRepo;

    /**
     * @var TemporaryFileService
     */
    protected $tempFileService;

    protected function mockRepo() : ITemporaryFileRepository
    {
        return new class(TemporaryFile::collection()) extends ArrayRepository implements ITemporaryFileRepository {
        };
    }

    protected function mockClock() : IClock
    {
        return new class() implements IClock {
            public function now() : \DateTimeImmutable
            {
                throw new \Exception('Should use UTC');
            }

            public function utcNow() : \DateTimeImmutable
            {
                return new \DateTimeImmutable('2000-01-01 00:00:00');
            }
        };
    }

    public function setUp()
    {
        $this->tempFileRepo    = $this->mockRepo();
        $this->tempFileService = new TemporaryFileService(
            $this->tempFileRepo,
            $this->mockClock(),
            new Repository([
                'dms.storage.temp-files.dir' => __DIR__,
            ])
        );
    }

    public function testStoreTempFile()
    {
        $file          = new File(__FILE__);
        $expirySeconds = 10;

        $tempFile = $this->tempFileService->storeTempFile($file, $expirySeconds);

        $this->assertSame($file, $tempFile->getFile());
        $this->assertSame('2000-01-01 00:00:10', $tempFile->getExpiry()->format('Y-m-d H:i:s'));

        $this->assertContains($tempFile, $this->tempFileRepo->getAll());
    }

    public function testStoreTempFiles()
    {
        $files         = [new File(__FILE__), new File(__FILE__), new File(__FILE__)];
        $expirySeconds = 10;

        $tempFiles = $this->tempFileService->storeTempFiles($files, $expirySeconds);

        $this->assertSame($files[0], $tempFiles[0]->getFile());
        $this->assertSame($files[1], $tempFiles[1]->getFile());
        $this->assertSame($files[2], $tempFiles[2]->getFile());
        $this->assertSame('2000-01-01 00:00:10', $tempFiles[0]->getExpiry()->format('Y-m-d H:i:s'));

        $this->assertEquals($tempFiles, $this->tempFileRepo->getAll());
    }

    public function testGetTempFile()
    {
        $expiredTime    = DateTime::fromString('1999-12-31 23:59:59');
        $notExpiredTime = DateTime::fromString('2000-01-01 00:00:01');

        $notExpiredFile = new TemporaryFile(
            'some_token',
            new File(__FILE__),
            $notExpiredTime
        );

        $expiredFile = new TemporaryFile(
            'some_expired_token',
            new File(__FILE__),
            $expiredTime
        );

        $this->tempFileRepo->saveAll([$notExpiredFile, $expiredFile]);

        $this->assertSame($notExpiredFile, $this->tempFileService->getTempFile('some_token'));

        $this->assertThrows(function () {
            $this->tempFileService->getTempFiles(['some_invalid_token']);
        }, EntityNotFoundException::class);

        $this->assertThrows(function () {
            $this->tempFileService->getTempFiles(['some_expired_token']);
        }, EntityNotFoundException::class);
    }
}
