<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\Mixed\Persistence\Infrastructure;

use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\EntityMapper;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\Mixed\Domain\TestEntity;


/**
 * The Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\Mixed\Domain\TestEntity entity mapper.
 */
class TestEntityMapper extends EntityMapper
{
    /**
     * Defines the entity mapper
     *
     * @param MapperDefinition $map
     *
     * @return void
     */
    protected function define(MapperDefinition $map)
    {
        $map->type(TestEntity::class);
        $map->toTable('test_entities');

        $map->idToPrimaryKey('id');

        /* TODO: TestEntity::MIXED */;


    }
}