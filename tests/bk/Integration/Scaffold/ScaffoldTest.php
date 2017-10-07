<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold;

use Dms\Web\Expressive\Scaffold\NamespaceDirectoryResolver;
use Dms\Web\Expressive\Tests\Integration\CmsIntegrationTest;
use Dms\Web\Expressive\Tests\Integration\Fixtures\Demo\DemoFixture;
use Illuminate\Contracts\Console\Kernel;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ScaffoldTest extends CmsIntegrationTest
{
    protected static function getFixture()
    {
        return new DemoFixture();
    }

    /**
     * @return Kernel
     */
    public function getConsole(): Kernel
    {
        return $this->app->make(Kernel::class);
    }

    protected function assertDirectoriesEqual(string $expected, string $actual, bool $normalizeLineReturns = true)
    {
        if (!is_dir($expected) && !is_dir($actual)) {
            return;
        }

        $expectedFiles = iterator_to_array(Finder::create()
            ->in($expected)
            ->files(), false);

        $expectedFiles = array_map(function (SplFileInfo $file) use ($expected) {
            return substr($file->getRealPath(), strlen(realpath($expected)));
        }, $expectedFiles);

        $actualFiles = iterator_to_array(Finder::create()
            ->in($actual)
            ->files(), false);

        $actualFiles = array_map(function (SplFileInfo $file) use ($actual) {
            return substr($file->getRealPath(), strlen(realpath($actual)));
        }, $actualFiles);

        sort($expectedFiles, SORT_STRING);
        sort($actualFiles, SORT_STRING);

        $this->assertEquals($expectedFiles, $actualFiles);

        foreach ($expectedFiles as $expectedFile) {
            $expectedContents = file_get_contents($expected . '/' . $expectedFile);
            $actualContents   = file_get_contents($actual . '/' . $expectedFile);

            if ($normalizeLineReturns) {
                $expectedContents = strtr($expectedContents, ["\r\n" => "\n"]);
                $actualContents   = strtr($actualContents, ["\r\n" => "\n"]);
            }

            $this->assertEquals($expectedContents, $actualContents);
        }
    }

    protected function mockNamespaceDirectoryResolver(array $namespaceDirectoryMap): NamespaceDirectoryResolver
    {
        return new class($namespaceDirectoryMap) extends NamespaceDirectoryResolver {
            /**
             * @var string[]
             */
            protected $namespaceDirectoryMap;

            /**
             *  constructor.
             *
             * @param \string[] $namespaceDirectoryMap
             */
            public function __construct(array $namespaceDirectoryMap)
            {
                $this->namespaceDirectoryMap = $namespaceDirectoryMap;
            }

            public function getDirectoryFor(string $namespace): string
            {
                return $this->namespaceDirectoryMap[$namespace];
            }
        };
    }
}
