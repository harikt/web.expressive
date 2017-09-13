<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold;

use Dms\Web\Laravel\Scaffold\NamespaceDirectoryResolver;
use Symfony\Component\Finder\Finder;


/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ScaffoldCmsTest extends ScaffoldTest
{
    public function scaffoldDomains()
    {
        $fixtures = [];

        foreach (Finder::create()
                     ->in(__DIR__ . '/Fixture')
                     ->depth('== 0')
                     ->directories()
                 as $directory) {
            $fixtures[] = [
                'name'             => $directory->getFilename(),
                'entity_namespace' => __NAMESPACE__ . '\\Fixture\\' . $directory->getFilename() . '\\Domain',
                'domain_path'      => $directory->getRealPath() . '/Domain',
                'cms_path'         => $directory->getRealPath() . '/Cms',
            ];
        }

        return $fixtures;
    }

    /**
     * @dataProvider scaffoldDomains
     */
    public function testScaffold(string $name, string $entityNamespace, string $domainPath, string $cmsPath)
    {
        $tempCmsPath = __DIR__ . '/temp/' . str_random();

        foreach (Finder::create()->files()->in($domainPath) as $file) {
            /** @var \SplFileInfo $file */
            require_once $file->getRealPath();
        }

        $this->app[NamespaceDirectoryResolver::class] = $this->mockNamespaceDirectoryResolver([
            __NAMESPACE__ . '\\Fixture\\' . $name . '\\Domain'               => $domainPath,
            __NAMESPACE__ . '\\Fixture\\' . $name . '\\Cms'                  => $tempCmsPath,
            __NAMESPACE__ . '\\Fixture\\' . $name . '\\Cms\\Modules'         => $tempCmsPath . '/Modules',
            __NAMESPACE__ . '\\Fixture\\' . $name . '\\Cms\\Modules\\Fields' => $tempCmsPath . '/Modules/Fields',
        ]);;

        $this->getConsole()->call('dms:scaffold:cms', [
            'package_name'          => $name,
            'entity_namespace'      => $entityNamespace,
            'output_namespace'      => __NAMESPACE__ . '\\Fixture\\' . $name . '\\Cms',
            'data_source_namespace' => 'Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\\' . $name . '\Persistence\Services',
        ]);

        $this->assertDirectoriesEqual($cmsPath, $tempCmsPath);
    }

    public function filterOptionTestCases()
    {
        return [
            ['*', true],
            [__NAMESPACE__ . '\\Fixture\\Simple\\Domain\\TestEntity', true],
            ['\\' . __NAMESPACE__ . '\\Fixture\\Simple\\Domain\\TestEntity', true],
            [__NAMESPACE__ . '\\Fixture\\Simple\\Domain\\*', true],
            [__NAMESPACE__ . '\\Fixture\\Simple\\*', true],
            [__NAMESPACE__ . '\\Fixture\\Simple\\Domain\\Test*', true],
            [__NAMESPACE__ . '\\Fixture\\Simple\\Domain\\*Entity', true],
            [__NAMESPACE__ . '\\Fixture\\Simple\\Domain\\AnotherEntity', false],
            [__NAMESPACE__ . '\\Fixture\\Simple\\Domain\\AnotherEntity', false],
            ['Abc', false],
            ['Abc\\*', false],
        ];
    }

    /**
     * @dataProvider filterOptionTestCases
     */
    public function testFilterOption(string $filter, $shouldExist)
    {
        $tempCmsPath = __DIR__ . '/temp/' . str_random();

        $this->app[NamespaceDirectoryResolver::class] = $this->mockNamespaceDirectoryResolver([
            __NAMESPACE__ . '\\Fixture\\Simple\\Domain'               => __DIR__ . '/Fixture/Simple/Domain',
            __NAMESPACE__ . '\\Fixture\\Simple\\Cms'                  => $tempCmsPath,
            __NAMESPACE__ . '\\Fixture\\Simple\\Cms\\Modules'         => $tempCmsPath . '/Modules',
            __NAMESPACE__ . '\\Fixture\\Simple\\Cms\\Modules\\Fields' => $tempCmsPath . '/Modules/Fields',
        ]);

        $this->getConsole()->call('dms:scaffold:cms', [
            'package_name'          => 'simple',
            'entity_namespace'      => __NAMESPACE__ . '\\Fixture\\Simple\\Domain',
            'output_namespace'      => __NAMESPACE__ . '\\Fixture\\Simple\\Cms',
            'data_source_namespace' => 'Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\Simple\Persistence\Services',
            '--filter'              => $filter,
        ]);

        if ($shouldExist) {
            $this->assertFileExists($tempCmsPath . '/Modules/TestEntityModule.php');
        } else {
            $this->assertFileNotExists($tempCmsPath . '/Modules/TestEntityModule.php');
        }
    }
}