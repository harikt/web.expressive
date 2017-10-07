<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Cms\Modules\Fields;

use Dms\Core\Common\Crud\Definition\Form\ValueObjectFieldDefinition;
use Dms\Core\Common\Crud\Form\ValueObjectField;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestGeoValueObject;
use Dms\Common\Structure\Field;
use Dms\Common\Structure\Geo\Country;

/**
 * The Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestGeoValueObject value object field.
 */
class TestGeoValueObjectField extends ValueObjectField
{
    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
    }

    /**
     * Defines the structure of this value object field.
     *
     * @param ValueObjectFieldDefinition $form
     *
     * @return void
     */
    protected function define(ValueObjectFieldDefinition $form)
    {
        $form->bindTo(TestGeoValueObject::class);

        $form->section('Details', [
            $form->field(
                Field::create('country', 'Country')->enum(Country::class, Country::getShortNameMap())->required()
            )->bindToProperty(TestGeoValueObject::COUNTRY),
            //
            $form->field(
                Field::create('nullable_country', 'Nullable Country')->enum(Country::class, Country::getShortNameMap())
            )->bindToProperty(TestGeoValueObject::NULLABLE_COUNTRY),
            //
            $form->field(
                Field::create('lat_lng', 'Lat Lng')->latLng()->required()
            )->bindToProperty(TestGeoValueObject::LAT_LNG),
            //
            $form->field(
                Field::create('nullable_lat_lng', 'Nullable Lat Lng')->latLng()
            )->bindToProperty(TestGeoValueObject::NULLABLE_LAT_LNG),
            //
            $form->field(
                Field::create('street_address', 'Street Address')->streetAddress()->required()
            )->bindToProperty(TestGeoValueObject::STREET_ADDRESS),
            //
            $form->field(
                Field::create('nullable_street_address', 'Nullable Street Address')->streetAddress()
            )->bindToProperty(TestGeoValueObject::NULLABLE_STREET_ADDRESS),
            //
            $form->field(
                Field::create('street_address_with_lat_lng', 'Street Address With Lat Lng')->streetAddressWithLatLng()->required()
            )->bindToProperty(TestGeoValueObject::STREET_ADDRESS_WITH_LAT_LNG),
            //
            $form->field(
                Field::create('nullable_street_address_with_lat_lng', 'Nullable Street Address With Lat Lng')->streetAddressWithLatLng()
            )->bindToProperty(TestGeoValueObject::NULLABLE_STREET_ADDRESS_WITH_LAT_LNG),
            //
        ]);
    }
}
