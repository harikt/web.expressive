<?php

namespace Dms\Web\Laravel\Tests\Unit\File\Persistence;

use Dms\Common\Structure\DateTime\DateTime;
use Dms\Common\Structure\FileSystem\File;
use Dms\Common\Structure\FileSystem\PathHelper;
use Dms\Core\Persistence\Db\Mapping\IOrm;
use Dms\Core\Tests\Persistence\Db\Integration\Mapping\DbIntegrationTest;
use Dms\Web\Laravel\File\Persistence\TempFileOrm;
use Dms\Web\Laravel\File\Persistence\TemporaryFileRepository;
use Dms\Web\Laravel\File\TemporaryFile;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TempFileOrmTest extends DbIntegrationTest
{
    /**
     * @return IOrm
     */
    protected function loadOrm()
    {
        return new TempFileOrm();
    }

    public function setUp()
    {
        parent::setUp();

        $this->repo = new TemporaryFileRepository($this->connection, $this->orm);
    }

    public function testSaveTempFile()
    {
        $this->repo->save(new TemporaryFile(
            'some_token',
            new File(__FILE__, 'abc.png'),
            DateTime::fromString('2010-01-01 00:00:01')
        ));

        $this->assertDatabaseDataSameAs([
            'temp_files' => [
                [
                    'id'               => 1,
                    'token'            => 'some_token',
                    'file'             => PathHelper::normalize(__FILE__),
                    'client_file_name' => 'abc.png',
                    'type'             => 'stored-file',
                    'expiry_time'      => '2010-01-01 00:00:01',
                ],
            ],
        ]);
    }

    public function testLoad()
    {
        $this->setDataInDb([
            'temp_files' => [
                [
                    'id'               => 1,
                    'token'            => 'some_token',
                    'file'             => PathHelper::normalize(__FILE__),
                    'type'             => 'stored-file',
                    'client_file_name' => null,
                    'expiry_time'      => '2010-01-01 00:00:01',
                ],
            ],
        ]);
        $file = new TemporaryFile(
            'some_token',
            new File(__FILE__),
            DateTime::fromString('2010-01-01 00:00:01')
        );
        $file->setId(1);

        $this->assertEquals($file, $this->repo->get(1));
    }
}