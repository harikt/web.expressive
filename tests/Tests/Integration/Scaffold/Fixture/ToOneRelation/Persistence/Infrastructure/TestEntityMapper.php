<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ToOneRelation\Persistence\Infrastructure;

use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\EntityMapper;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ToOneRelation\Domain\TestEntity;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ToOneRelation\Domain\TestRelatedEntity;

/**
 * The Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ToOneRelation\Domain\TestEntity entity mapper.
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

        $map->relation(TestEntity::RELATED)
            ->to(TestRelatedEntity::class)
            ->toOne()
            ->identifying()
            ->withBidirectionalRelation(TestRelatedEntity::PARENT)
            ->withParentIdAs('test_entity_id');


    }
}