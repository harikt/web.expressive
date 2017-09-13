<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\Enum\Cms;

use Dms\Core\Package\Definition\PackageDefinition;
use Dms\Core\Package\Package;


/**
 * The Enum package.
 */
class EnumPackage extends Package
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
        $package->name('Enum');

        $package->metadata([
            'icon' => '',
        ]);

        $package->modules([

        ]);
    }
}