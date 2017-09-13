<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Persistence\Infrastructure;

use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\IndependentValueObjectMapper;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestGeoValueObject;
use Dms\Common\Structure\Geo\Persistence\LatLngMapper;
use Dms\Common\Structure\Geo\Persistence\StreetAddressMapper;
use Dms\Common\Structure\Geo\Persistence\StreetAddressWithLatLngMapper;

/**
 * The Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestGeoValueObject value object mapper.
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

        $map->embeddedCollection(TestGeoValueObject::LAT_LNG)
            ->toTable('test_geo_value_object_lat_lng')
            ->withPrimaryKey('id')
            ->withForeignKeyToParentAs('test_geo_value_object_id')
            ->using(new LatLngMapper('lat_lng_lat', 'lat_lng_lng'));

        $map->embeddedCollection(TestGeoValueObject::STREET_ADDRESS)
            ->toTable('test_geo_value_object_street_address')
            ->withPrimaryKey('id')
            ->withForeignKeyToParentAs('test_geo_value_object_id')
            ->using(new StreetAddressMapper('street_address'));

        $map->embeddedCollection(TestGeoValueObject::STREET_ADDRESS_WITH_LAT_LNG)
            ->toTable('test_geo_value_object_street_address_with_lat_lng')
            ->withPrimaryKey('id')
            ->withForeignKeyToParentAs('test_geo_value_object_id')
            ->using(new StreetAddressWithLatLngMapper('street_address_with_lat_lng_address', 'street_address_with_lat_lng_lat', 'street_address_with_lat_lng_lng'));


    }
}