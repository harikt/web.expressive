<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Persistence\Db\Migration;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Persistence\Db\Connection\IConnection;
use Dms\Core\Persistence\Db\Doctrine\DoctrineConnection;
use Dms\Core\Persistence\Db\Mapping\IOrm;
use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Support\Composer;

/**
 * The auto migrate command
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class AutoGenerateMigrationCommand extends BaseCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'dms:make:migration {name : The name of the migration.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-generates a new migration file to sync the db with the current state of the orm';

    /**
     * @var Composer
     */
    private $composer;

    /**
     * AutoGenerateMigrationCommand constructor.
     *
     * @param Composer $composer
     */
    public function __construct(Composer $composer)
    {
        parent::__construct();
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @param LaravelMigrationGenerator $autoMigrationGenerator
     * @param IConnection               $connection
     * @param IOrm                      $orm
     */
    public function fire(
        LaravelMigrationGenerator $autoMigrationGenerator,
        IConnection $connection,
        IOrm $orm
    ) {
        InvalidArgumentException::verifyInstanceOf(__METHOD__, 'connection', $connection, DoctrineConnection::class);

        $file = $autoMigrationGenerator->generateMigration(
            $connection,
            $orm,
            $this->input->getArgument('name')
        );

        if (!$file) {
            $this->line("<info>No Migration Generated: Schema has not changed.</info>");
        } else {
            $this->line("<info>Created Migration:</info> {$file}");
        }

        $this->composer->dumpAutoloads();
    }
}
