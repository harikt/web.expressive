<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Persistence\Infrastructure;

use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\EntityMapper;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Domain\TestEntity;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Domain\TestRelatedEntity;

/**
 * The Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Domain\TestEntity entity mapper.
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
            ->toMany()
            ->withBidirectionalRelation(TestRelatedEntity::PARENT)
            ->throughJoinTable('test_entity_test_related_entities')
            ->withParentIdAs('test_entity_id')
            ->withRelatedIdAs('test_related_entity_id');
    }
}
