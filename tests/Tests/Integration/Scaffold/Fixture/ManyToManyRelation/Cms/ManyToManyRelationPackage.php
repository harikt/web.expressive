<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Cms;

use Dms\Core\Package\Definition\PackageDefinition;
use Dms\Core\Package\Package;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Cms\Modules\TestEntityModule;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Cms\Modules\TestRelatedEntityModule;

/**
 * The ManyToManyRelation package.
 */
class ManyToManyRelationPackage extends Package
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
        $package->name('ManyToManyRelation');

        $package->metadata([
            'icon' => '',
        ]);

        $package->modules([
            'test-entity' => TestEntityModule::class,
            'test-related-entity' => TestRelatedEntityModule::class,
        ]);
    }
}