<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\Simple\Cms;

use Dms\Core\Package\Definition\PackageDefinition;
use Dms\Core\Package\Package;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\Simple\Cms\Modules\TestEntityModule;

/**
 * The Simple package.
 */
class SimplePackage extends Package
{
    /**
     * Defines the structure of this cms package.
     *
     * @param PackageDefinition $package
     *
     * @return void
     */
    protected function define(PackageDefinition $package)
    {
        $package->name('Simple');

        $package->metadata([
            'icon' => '',
        ]);

        $package->modules([
            'test-entity' => TestEntityModule::class,
        ]);
    }
}
