<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Cms;

use Dms\Core\Package\Definition\PackageDefinition;
use Dms\Core\Package\Package;


/**
 * The ValueObjectCollection package.
 */
class ValueObjectCollectionPackage extends Package
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
        $package->name('ValueObjectCollection');

        $package->metadata([
            'icon' => '',
        ]);

        $package->modules([

        ]);
    }
}