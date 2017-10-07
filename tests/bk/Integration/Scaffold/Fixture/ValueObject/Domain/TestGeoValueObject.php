<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Domain;

use Dms\Common\Structure\Geo\Country;
use Dms\Common\Structure\Geo\LatLng;
use Dms\Common\Structure\Geo\StreetAddress;
use Dms\Common\Structure\Geo\StreetAddressWithLatLng;
use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\ValueObject;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TestGeoValueObject extends ValueObject
{
    const COUNTRY = 'country';
    const NULLABLE_COUNTRY = 'nullableCountry';
    const LAT_LNG = 'latLng';
    const NULLABLE_LAT_LNG = 'nullableLatLng';
    const STREET_ADDRESS = 'streetAddress';
    const NULLABLE_STREET_ADDRESS = 'nullableStreetAddress';
    const STREET_ADDRESS_WITH_LAT_LNG = 'streetAddressWithLatLng';
    const NULLABLE_STREET_ADDRESS_WITH_LAT_LNG = 'nullableStreetAddressWithLatLng';

    /**
     * @var Country
     */
    public $country;

    /**
     * @var Country|null
     */
    public $nullableCountry;

    /**
     * @var LatLng
     */
    public $latLng;

    /**
     * @var LatLng|null
     */
    public $nullableLatLng;

    /**
     * @var StreetAddress
     */
    public $streetAddress;

    /**
     * @var StreetAddress|null
     */
    public $nullableStreetAddress;

    /**
     * @var StreetAddressWithLatLng
     */
    public $streetAddressWithLatLng;

    /**
     * @var StreetAddressWithLatLng|null
     */
    public $nullableStreetAddressWithLatLng;

    /**
     * Defines the structure of this class.
     *
     * @param ClassDefinition $class
     */
    protected function define(ClassDefinition $class)
    {
        $class->property($this->country)->asObject(Country::class);

        $class->property($this->nullableCountry)->nullable()->asObject(Country::class);

        $class->property($this->latLng)->asObject(LatLng::class);

        $class->property($this->nullableLatLng)->nullable()->asObject(LatLng::class);

        $class->property($this->streetAddress)->asObject(StreetAddress::class);

        $class->property($this->nullableStreetAddress)->nullable()->asObject(StreetAddress::class);

        $class->property($this->streetAddressWithLatLng)->asObject(StreetAddressWithLatLng::class);

        $class->property($this->nullableStreetAddressWithLatLng)->nullable()->asObject(StreetAddressWithLatLng::class);
    }
}
