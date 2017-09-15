<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Persistence\Db\Migration;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Persistence\Db\Connection\IConnection;
use Dms\Core\Persistence\Db\Doctrine\DoctrineConnection;
use Dms\Core\Persistence\Db\Mapping\IOrm;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The auto migrate command
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class AutoGenerateMigrationCommand extends Command
{
    protected $autoMigrationGenerator;

    protected $connection;

    protected $orm;

    /**
     * @param LaravelMigrationGenerator $autoMigrationGenerator
     * @param IConnection               $connection
     * @param IOrm                      $orm
     */
    public function __construct(
        LaravelMigrationGenerator $autoMigrationGenerator,
        IConnection $connection,
        IOrm $orm
    ) {
        $this->autoMigrationGenerator = $autoMigrationGenerator;
        $this->connection = $connection;
        $this->orm = $orm;
    }

    protected function configure()
    {
        $this
            ->setName('dms:make:migration')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the migration.')
            ->setDescription('Auto-generates a new migration file to sync the db with the current state of the orm')
            ;
    }

    /**
     * Execute the console command.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        InvalidArgumentException::verifyInstanceOf(__METHOD__, 'connection', $connection, DoctrineConnection::class);

        $file = $autoMigrationGenerator->generateMigration(
            $connection,
            $orm,
            $this->input->getArgument('name')
        );

        if (!$file) {
            $output->writeln("<info>No Migration Generated: Schema has not changed.</info>");
        } else {
            $output->writeln("<info>Created Migration:</info> {$file}");
        }
    }
}
