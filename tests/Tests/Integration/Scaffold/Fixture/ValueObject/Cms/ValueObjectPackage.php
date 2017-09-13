<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObject\Cms;

use Dms\Core\Package\Definition\PackageDefinition;
use Dms\Core\Package\Package;


/**
 * The ValueObject package.
 */
class ValueObjectPackage extends Package
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
        $package->name('ValueObject');

        $package->metadata([
            'icon' => '',
        ]);

        $package->modules([

        ]);
    }
}