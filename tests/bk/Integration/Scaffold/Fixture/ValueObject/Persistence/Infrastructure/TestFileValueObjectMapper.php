<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Persistence\Infrastructure;

use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\IndependentValueObjectMapper;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestFileValueObject;
use Dms\Common\Structure\FileSystem\Persistence\FileMapper;
use Dms\Common\Structure\FileSystem\Persistence\ImageMapper;

/**
 * The Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestFileValueObject value object mapper.
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

        $map->embedded(TestFileValueObject::FILE)
            ->using(new FileMapper('file', 'file_file_name', public_path('app/test_file_value_object')));

        $map->embedded(TestFileValueObject::NULLABLE_FILE)
            ->withIssetColumn('nullable_file')
            ->using(new FileMapper('nullable_file', 'nullable_file_file_name', public_path('app/test_file_value_object')));

        $map->embedded(TestFileValueObject::IMAGE)
            ->using(new ImageMapper('image', 'image_file_name', public_path('app/test_file_value_object')));

        $map->embedded(TestFileValueObject::NULLABLE_IMAGE)
            ->withIssetColumn('nullable_image')
            ->using(new ImageMapper('nullable_image', 'nullable_image_file_name', public_path('app/test_file_value_object')));
    }
}
