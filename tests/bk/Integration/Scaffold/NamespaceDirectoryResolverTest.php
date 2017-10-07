<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold;

use Dms\Web\Expressive\Scaffold\NamespaceDirectoryResolver;
use Dms\Web\Expressive\Tests\Integration\CmsIntegrationTest;
use Dms\Web\Expressive\Tests\Integration\Fixtures\Demo\DemoFixture;

/**
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class NamespaceDirectoryResolverTest extends CmsIntegrationTest
{
    protected static function getFixture()
    {
        return new DemoFixture();
    }

    public function testNamespace()
    {
        $resolver = new NamespaceDirectoryResolver();
        $this->assertSame(__DIR__ . DIRECTORY_SEPARATOR, $resolver->getDirectoryFor(__NAMESPACE__));
        $this->assertSame(realpath(__DIR__ . '/../../../../vendor/dms-org/core/src') . DIRECTORY_SEPARATOR, $resolver->getDirectoryFor('Dms\\Core'));
        $this->assertSame(realpath(__DIR__ . '/../../../../src') . DIRECTORY_SEPARATOR, $resolver->getDirectoryFor('Dms\\Web\\Laravel'));
        $this->assertSame(realpath(__DIR__ . '/../../../../src/Scaffold') . DIRECTORY_SEPARATOR, $resolver->getDirectoryFor('Dms\\Web\\Laravel\\Scaffold'));
        $this->assertSame(realpath(__DIR__ . '/../../../../src') . DIRECTORY_SEPARATOR . 'Another' . DIRECTORY_SEPARATOR, $resolver->getDirectoryFor('Dms\\Web\\Laravel\\Another'));
    }
}
