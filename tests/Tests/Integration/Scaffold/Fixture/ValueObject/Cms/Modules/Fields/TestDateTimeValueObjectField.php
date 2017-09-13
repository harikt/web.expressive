<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObject\Cms\Modules\Fields;

use Dms\Core\Common\Crud\Definition\Form\ValueObjectFieldDefinition;
use Dms\Core\Common\Crud\Form\ValueObjectField;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestDateTimeValueObject;
use Dms\Common\Structure\Field;

/**
 * The Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestDateTimeValueObject value object field.
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
                Field::create('date_time', 'Date Time')->dateTime()->required()
            )->bindToProperty(TestDateTimeValueObject::DATE_TIME),
            //
            $form->field(
                Field::create('nullable_date_time', 'Nullable Date Time')->dateTime()
            )->bindToProperty(TestDateTimeValueObject::NULLABLE_DATE_TIME),
            //
            $form->field(
                Field::create('date', 'Date')->date()->required()
            )->bindToProperty(TestDateTimeValueObject::DATE),
            //
            $form->field(
                Field::create('nullable_date', 'Nullable Date')->date()
            )->bindToProperty(TestDateTimeValueObject::NULLABLE_DATE),
            //
            $form->field(
                Field::create('time_of_day', 'Time Of Day')->time()->required()
            )->bindToProperty(TestDateTimeValueObject::TIME_OF_DAY),
            //
            $form->field(
                Field::create('nullable_time_of_day', 'Nullable Time Of Day')->time()
            )->bindToProperty(TestDateTimeValueObject::NULLABLE_TIME_OF_DAY),
            //
            $form->field(
                Field::create('timezoned_date_time', 'Timezoned Date Time')->dateTimeWithTimezone()->required()
            )->bindToProperty(TestDateTimeValueObject::TIMEZONED_DATE_TIME),
            //
            $form->field(
                Field::create('nullable_timezoned_date_time', 'Nullable Timezoned Date Time')->dateTimeWithTimezone()
            )->bindToProperty(TestDateTimeValueObject::NULLABLE_TIMEZONED_DATE_TIME),
            //
        ]);

    }
}