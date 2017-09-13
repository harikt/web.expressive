<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Cms\Modules\Fields;

use Dms\Core\Common\Crud\Definition\Form\ValueObjectFieldDefinition;
use Dms\Core\Common\Crud\Form\ValueObjectField;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestDateTimeValueObject;
use Dms\Common\Structure\Field;

/**
 * The Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestDateTimeValueObject value object field.
 */
class TestDateTimeValueObjectField extends ValueObjectField
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
        $form->bindTo(TestDateTimeValueObject::class);

        $form->section('Details', [
            $form->field(
                Field::create('date_time', 'Date Time')->arrayOf(
                    Field::element()->dateTime()->required()
                )
            )->bindToProperty(TestDateTimeValueObject::DATE_TIME),
            //
            $form->field(
                Field::create('date', 'Date')->arrayOf(
                    Field::element()->date()->required()
                )
            )->bindToProperty(TestDateTimeValueObject::DATE),
            //
            $form->field(
                Field::create('time_of_day', 'Time Of Day')->arrayOf(
                    Field::element()->time()->required()
                )
            )->bindToProperty(TestDateTimeValueObject::TIME_OF_DAY),
            //
            $form->field(
                Field::create('timezoned_date_time', 'Timezoned Date Time')->arrayOf(
                    Field::element()->dateTimeWithTimezone()->required()
                )
            )->bindToProperty(TestDateTimeValueObject::TIMEZONED_DATE_TIME),
            //
        ]);

    }
}