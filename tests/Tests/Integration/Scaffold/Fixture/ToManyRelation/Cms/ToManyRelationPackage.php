<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ToManyRelation\Cms;

use Dms\Core\Package\Definition\PackageDefinition;
use Dms\Core\Package\Package;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ToManyRelation\Cms\Modules\TestEntityModule;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ToManyRelation\Cms\Modules\TestRelatedEntityModule;

/**
 * The ToManyRelation package.
 */
class ToManyRelationPackage extends Package
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
        $package->name('ToManyRelation');

        $package->metadata([
            'icon' => '',
        ]);

        $package->modules([
            'test-entity' => TestEntityModule::class,
            'test-related-entity' => TestRelatedEntityModule::class,
        ]);
    }
}