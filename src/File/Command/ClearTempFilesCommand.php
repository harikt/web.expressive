<?php declare(strict_types=1);

namespace Dms\Web\Expressive\File\Command;

use Dms\Core\Util\IClock;
use Dms\Web\Expressive\File\Persistence\ITemporaryFileRepository;
use Dms\Web\Expressive\File\TemporaryFile;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;

/**
 * The clear temp file command.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ClearTempFilesCommand extends Command
{

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'dms:clear-temp-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears the expired temporary files';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * ClearTempFilesCommand constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    public function fire(ITemporaryFileRepository $tempFileRepo, IClock $clock)
    {
        $expiredFiles = $tempFileRepo->matching(
            $tempFileRepo->criteria()
                ->whereSatisfies(TemporaryFile::expiredSpec($clock))
        );

        foreach ($expiredFiles as $file) {
            if ($file->getFile()->exists()) {
                $this->filesystem->delete($file->getFile()->getFullPath());
                $this->output->writeln("<info>Deleted {$file->getFile()->getFullPath()}</info>");
            }
        }

        $tempFileRepo->removeAll($expiredFiles);
    }
}
