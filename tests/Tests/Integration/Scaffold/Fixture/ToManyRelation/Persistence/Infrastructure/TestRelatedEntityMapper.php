<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ToManyRelation\Persistence\Infrastructure;

use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\EntityMapper;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ToManyRelation\Domain\TestRelatedEntity;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ToManyRelation\Domain\TestEntity;

/**
 * The Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ToManyRelation\Domain\TestRelatedEntity entity mapper.
 */
class TestRelatedEntityMapper extends EntityMapper
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
        $map->type(TestRelatedEntity::class);
        $map->toTable('test_related_entities');

        $map->idToPrimaryKey('id');

        $map->column('test_entity_id')->asUnsignedInt();
        $map->relation(TestRelatedEntity::PARENT)
            ->to(TestEntity::class)
            ->manyToOne()
            ->withBidirectionalRelation(TestEntity::RELATED)
            ->withRelatedIdAs('test_entity_id');


    }
}