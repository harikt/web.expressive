<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Persistence\Infrastructure;

use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\IndependentValueObjectMapper;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestFileValueObject;
use Dms\Common\Structure\FileSystem\Persistence\FileMapper;

/**
 * The Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestFileValueObject value object mapper.
 */
class TestFileValueObjectMapper extends IndependentValueObjectMapper
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
        $map->type(TestFileValueObject::class);

        $map->embeddedCollection(TestFileValueObject::FILE)
            ->toTable('test_file_value_object_file')
            ->withPrimaryKey('id')
            ->withForeignKeyToParentAs('test_file_value_object_id')
            ->using(new FileMapper('file', 'file_file_name', public_path('app/test_file_value_object')));

        $map->embeddedCollection(TestFileValueObject::IMAGE)
            ->toTable('test_file_value_object_image')
            ->withPrimaryKey('id')
            ->withForeignKeyToParentAs('test_file_value_object_id')
            ->using(new FileMapper('image', 'image_file_name', public_path('app/test_file_value_object')));
    }
}
