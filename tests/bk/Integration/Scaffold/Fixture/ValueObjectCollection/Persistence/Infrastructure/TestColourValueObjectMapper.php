<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Persistence\Infrastructure;

use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\IndependentValueObjectMapper;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestColourValueObject;
use Dms\Common\Structure\Colour\Mapper\ColourMapper;
use Dms\Common\Structure\Colour\Mapper\TransparentColourMapper;

/**
 * The Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestColourValueObject value object mapper.
 */
class TestColourValueObjectMapper extends IndependentValueObjectMapper
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
        $map->type(TestColourValueObject::class);

        $map->embeddedCollection(TestColourValueObject::COLOUR)
            ->toTable('test_colour_value_object_colour')
            ->withPrimaryKey('id')
            ->withForeignKeyToParentAs('test_colour_value_object_id')
            ->using(ColourMapper::asHexString('colour'));

        $map->embeddedCollection(TestColourValueObject::TRANSPARENT_COLOUR)
            ->toTable('test_colour_value_object_transparent_colour')
            ->withPrimaryKey('id')
            ->withForeignKeyToParentAs('test_colour_value_object_id')
            ->using(TransparentColourMapper::asRgbaString('transparent_colour'));
    }
}
