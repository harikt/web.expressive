<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectRelation\Persistence\Infrastructure;

use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\EntityMapper;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectRelation\Domain\TestEntity;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectRelation\Persistence\Infrastructure\TestValueObjectMapper;

/**
 * The Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectRelation\Domain\TestEntity entity mapper.
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

        $map->embedded(TestEntity::VALUE_OBJECT)
            ->using(new TestValueObjectMapper());

        $map->embedded(TestEntity::NULLABLE_VALUE_OBJECT)
            ->withIssetColumn('has_nullable_value_object')
            ->using(new TestValueObjectMapper());

        $map->embeddedCollection(TestEntity::VALUE_OBJECT_COLLECTION)
            ->toTable('test_entity_value_object_collections')
            ->withPrimaryKey('id')
            ->withForeignKeyToParentAs('test_entity_id')
            ->using(new TestValueObjectMapper());
    }
}
