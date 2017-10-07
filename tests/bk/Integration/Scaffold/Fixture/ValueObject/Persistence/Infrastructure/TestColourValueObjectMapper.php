<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Persistence\Infrastructure;

use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\IndependentValueObjectMapper;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestColourValueObject;
use Dms\Common\Structure\Colour\Mapper\ColourMapper;
use Dms\Common\Structure\Colour\Mapper\TransparentColourMapper;

/**
 * The Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestColourValueObject value object mapper.
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

        $map->embedded(TestColourValueObject::COLOUR)
            ->using(ColourMapper::asHexString('colour'));

        $map->embedded(TestColourValueObject::NULLABLE_COLOUR)
            ->withIssetColumn('nullable_colour')
            ->using(ColourMapper::asHexString('nullable_colour'));

        $map->embedded(TestColourValueObject::TRANSPARENT_COLOUR)
            ->using(TransparentColourMapper::asRgbaString('transparent_colour'));

        $map->embedded(TestColourValueObject::NULLABLE_TRANSPARENT_COLOUR)
            ->withIssetColumn('nullable_transparent_colour')
            ->using(TransparentColourMapper::asRgbaString('nullable_transparent_colour'));
    }
}
