<?php

namespace Dms\Web\Expressive\Tests\Integration\Fixtures;

use Dms\Core\Persistence\Db\Connection\IConnection;
use Dms\Core\Persistence\Db\Mapping\IOrm;
use Dms\Web\Expressive\Persistence\Db\Migration\LaravelMigrationGenerator;
use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Filesystem\Filesystem;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class DmsFixture
{
    /**
     * @return string
     */
    abstract public function getCmsClass();

    /**
     * @return string
     */
    abstract public function getOrmClass();

    /**
     * @param Application $app
     *
     * @return void
     */
    protected function seed(Application $app)
    {
    }

    /**
     * @param Application $app
     *
     * @return string
     */
    public function setUpBeforeClass(Application $app)
    {
        $stubFileDirectory = dirname($this->dbStubFile());
        if (!is_dir($stubFileDirectory)) {
            mkdir($stubFileDirectory, 0777, true);
        }

        /** @var LaravelMigrationGenerator $migrationGenerator */
        $migrationsPath = $this->migrationsPath();
        if (!is_dir($migrationsPath)) {
            mkdir($migrationsPath, 0777, true);
        }

        $migrationsPath = realpath($migrationsPath);
        file_put_contents($this->dbFile(), '');
        foreach (glob($this->migrationsPath() . '/*.*') as $migrationFile) {
            unlink($migrationFile);
        }

        $app['config']->set('database.default', 'testing-stub');
        $app['config']->set('database.connections.testing-stub', [
            'driver'   => 'sqlite',
            'database' => $this->dbStubFile(),
            'prefix'   => '',
        ]);

        $migrationGenerator = new LaravelMigrationGenerator(
            app(MigrationCreator::class),
            app(Filesystem::class),
            $migrationsPath
        );

        app()->resolving(Migrator::class, function (Migrator $migrator) {
            $migrator->path($this->migrationsPath());
        });

        if (!@file_get_contents($this->dbStubFile())) {
            file_put_contents($this->dbStubFile(), '');

            $migrationFile = $migrationGenerator->generateMigration(
                $app->make(IConnection::class),
                $app->make(IOrm::class),
                basename(str_replace('\\', '-', get_class($this))) . '-' . str_random(5)
            );

            if ($migrationFile) {
                $console = $this->getConsole($app);
                $console->call('migrate', [
                    '--database' => 'testing-stub',
                ]);
            }

            $this->seed($app);
        }
    }

    public function setUp(Application $app)
    {
        $app->setBasePath(realpath(__DIR__ . '/../../../../'));

        // Setup default database to use sqlite
        /** @var Application|Container $app */
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => $this->dbFile(),
            'prefix'   => '',
        ]);
        $app->forgetInstance(IConnection::class);

        copy($this->dbStubFile(), $this->dbFile());
    }

    /**
     * @return string
     */
    protected function migrationsPath()
    {
        return __DIR__ . '/../temp/migrations';
    }

    /**
     * @return string
     */
    protected function dbStubFile()
    {
        return  __DIR__ . '/../temp/db.stub.sqlite';
    }

    /**
     * @return string
     */
    protected function dbFile()
    {
        return __DIR__ . '/../temp/db.sqlite';
    }

    /**
     * @param Application $app
     *
     * @return \Illuminate\Contracts\Console\Kernel
     */
    protected function getConsole(Application $app)
    {
        return $app[\Illuminate\Contracts\Console\Kernel::class];
    }

    /**
     * @param Application $app
     * @param string      $class
     *
     * @return void
     */
    protected function runSeeder(Application $app, $class)
    {
        $this->getConsole($app)->call('db:seed', [
            '--class' => $class,
        ]);
    }
}
