<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Persistence\Infrastructure;

use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\EntityMapper;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Domain\TestRelatedEntity;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Domain\TestEntity;

/**
 * The Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Domain\TestRelatedEntity entity mapper.
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

        $map->relation(TestRelatedEntity::PARENT)
            ->to(TestEntity::class)
            ->toMany()
            ->withBidirectionalRelation(TestEntity::RELATED)
            ->throughJoinTable('test_entity_test_related_entities')
            ->withParentIdAs('test_related_entity_id')
            ->withRelatedIdAs('test_entity_id');
    }
}
