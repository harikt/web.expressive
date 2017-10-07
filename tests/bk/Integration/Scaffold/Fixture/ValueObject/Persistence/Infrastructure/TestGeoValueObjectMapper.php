<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Persistence\Infrastructure;

use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\IndependentValueObjectMapper;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestGeoValueObject;
use Dms\Common\Structure\Geo\Persistence\LatLngMapper;
use Dms\Common\Structure\Geo\Persistence\StreetAddressMapper;
use Dms\Common\Structure\Geo\Persistence\StreetAddressWithLatLngMapper;

/**
 * The Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestGeoValueObject value object mapper.
 */
class TestGeoValueObjectMapper extends IndependentValueObjectMapper
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
        $map->type(TestGeoValueObject::class);

        $map->enum(TestGeoValueObject::COUNTRY)->to('country')->asVarchar(2);

        $map->enum(TestGeoValueObject::NULLABLE_COUNTRY)->to('nullable_country')->nullable()->asVarchar(2);

        $map->embedded(TestGeoValueObject::LAT_LNG)
            ->using(new LatLngMapper('lat_lng_lat', 'lat_lng_lng'));

        $map->embedded(TestGeoValueObject::NULLABLE_LAT_LNG)
            ->withIssetColumn('nullable_lat_lng_lat')
            ->using(new LatLngMapper('nullable_lat_lng_lat', 'nullable_lat_lng_lng'));

        $map->embedded(TestGeoValueObject::STREET_ADDRESS)
            ->using(new StreetAddressMapper('street_address'));

        $map->embedded(TestGeoValueObject::NULLABLE_STREET_ADDRESS)
            ->withIssetColumn('nullable_street_address')
            ->using(new StreetAddressMapper('nullable_street_address'));

        $map->embedded(TestGeoValueObject::STREET_ADDRESS_WITH_LAT_LNG)
            ->using(new StreetAddressWithLatLngMapper('street_address_with_lat_lng_address', 'street_address_with_lat_lng_lat', 'street_address_with_lat_lng_lng'));

        $map->embedded(TestGeoValueObject::NULLABLE_STREET_ADDRESS_WITH_LAT_LNG)
            ->withIssetColumn('nullable_street_address_with_lat_lng_address')
            ->using(new StreetAddressWithLatLngMapper('nullable_street_address_with_lat_lng_address', 'nullable_street_address_with_lat_lng_lat', 'nullable_street_address_with_lat_lng_lng'));
    }
}
