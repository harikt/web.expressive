<?php declare(strict_types=1);

namespace Dms\Web\Expressive\File;

use Dms\Common\Structure\DateTime\DateTime;
use Dms\Common\Structure\FileSystem\PathHelper;
use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\File\IFile;
use Dms\Core\File\IUploadedFile;
use Dms\Core\Model\EntityNotFoundException;
use Dms\Core\Util\IClock;
use Dms\Web\Expressive\File\Persistence\ITemporaryFileRepository;
use Illuminate\Config\Repository;

/**
 * The temporary file service class.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TemporaryFileService implements ITemporaryFileService
{
    /**
     * @var ITemporaryFileRepository
     */
    protected $repo;

    /**
     * @var IClock
     */
    protected $clock;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * TemporaryFileService constructor.
     *
     * @param ITemporaryFileRepository $repo
     * @param IClock                   $clock
     * @param Repository               $config
     */
    public function __construct(ITemporaryFileRepository $repo, IClock $clock, Repository $config)
    {
        $this->repo   = $repo;
        $this->clock  = $clock;
        $this->config = $config;
    }

    /**
     * Stores the supplied file as a temporary file.
     *
     * @param IFile $file
     * @param int   $expirySeconds The amount of seconds from now for the file to expire
     *
     * @return TemporaryFile
     */
    public function storeTempFile(IFile $file, int $expirySeconds) : TemporaryFile
    {
        return $this->storeTempFiles([$file], $expirySeconds)[0];
    }

    /**
     * Stores the supplied files as temporary files.
     *
     * @param IFile[] $files
     * @param int     $expirySeconds The amount of seconds from now for the file to expire
     *
     * @return TemporaryFile[]
     */
    public function storeTempFiles(array $files, int $expirySeconds) : array
    {
        InvalidArgumentException::verifyAllInstanceOf(__METHOD__, 'files', $files, IFile::class);

        $tempUploadDirectory = $this->config->get('dms.storage.temp-files.dir');
        $tempFiles = [];

        foreach ($files as $key => $file) {
            if ($file instanceof IUploadedFile) {
                $fileName = str_random(32);
                $file     = $file->moveTo(PathHelper::combine($tempUploadDirectory, $fileName));
            }

            $tempFiles[$key] = new TemporaryFile(
                str_random(40),
                $file,
                (new DateTime($this->clock->utcNow()))->addSeconds($expirySeconds)
            );
        }

        $this->repo->saveAll($tempFiles);

        return $tempFiles;
    }

    /**
     * Gets the supplied temp file from the token
     *
     * @param string $token
     *
     * @return TemporaryFile
     * @throws EntityNotFoundException
     */
    public function getTempFile(string $token) : TemporaryFile
    {
        return $this->getTempFiles([$token])[0];
    }

    /**
     * Gets the temp files from the supplied tokens
     *
     * @param string[] $tokens
     *
     * @return TemporaryFile[]
     * @throws EntityNotFoundException
     */
    public function getTempFiles(array $tokens) : array
    {
        $files = $this->repo->matching(
            $this->repo->criteria()
                ->whereIn(TemporaryFile::TOKEN, $tokens)
                ->whereSatisfies(TemporaryFile::notExpiredSpec($this->clock))
        );

        if (count($files) !== count($tokens)) {
            throw new EntityNotFoundException(TemporaryFile::class, implode(',', $tokens), TemporaryFile::TOKEN);
        }

        return $files;
    }
}
