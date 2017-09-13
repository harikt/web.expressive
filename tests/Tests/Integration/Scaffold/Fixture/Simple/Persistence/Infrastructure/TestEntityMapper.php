<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\Simple\Persistence\Infrastructure;

use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\EntityMapper;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\Simple\Domain\TestEntity;


/**
 * The Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\Simple\Domain\TestEntity entity mapper.
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

        $map->property(TestEntity::STRING)->to('string')->asVarchar(255);

        $map->property(TestEntity::INT)->to('int')->asInt();

        $map->property(TestEntity::FLOAT)->to('float')->asDecimal(16, 8);

        $map->property(TestEntity::BOOL)->to('bool')->asBool();


    }
}