<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain;

use Dms\Common\Structure\Geo\Country;
use Dms\Common\Structure\Geo\LatLng;
use Dms\Common\Structure\Geo\StreetAddress;
use Dms\Common\Structure\Geo\StreetAddressWithLatLng;
use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\ValueObject;
use Dms\Core\Model\ValueObjectCollection;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TestGeoValueObject extends ValueObject
{
    const LAT_LNG = 'latLng';
    const STREET_ADDRESS = 'streetAddress';
    const STREET_ADDRESS_WITH_LAT_LNG = 'streetAddressWithLatLng';

    /**
     * @var ValueObjectCollection|LatLng[]
     */
    public $latLng;

    /**
     * @var ValueObjectCollection|StreetAddress[]
     */
    public $streetAddress;

    /**
     * @var ValueObjectCollection|StreetAddressWithLatLng[]
     */
    public $streetAddressWithLatLng;

    /**
     * Defines the structure of this class.
     *
     * @param ClassDefinition $class
     */
    protected function define(ClassDefinition $class)
    {
        $class->property($this->latLng)->asType(LatLng::collectionType());

        $class->property($this->streetAddress)->asType(StreetAddress::collectionType());

        $class->property($this->streetAddressWithLatLng)->asType(StreetAddressWithLatLng::collectionType());
    }
}