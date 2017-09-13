<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObject\Domain;

use Dms\Common\Structure\DateTime\Date;
use Dms\Common\Structure\DateTime\DateTime;
use Dms\Common\Structure\DateTime\TimeOfDay;
use Dms\Common\Structure\DateTime\TimezonedDateTime;
use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\ValueObject;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TestDateTimeValueObject extends ValueObject
{
    const DATE_TIME = 'dateTime';
    const NULLABLE_DATE_TIME = 'nullableDateTime';
    const DATE = 'date';
    const NULLABLE_DATE = 'nullableDate';
    const TIME_OF_DAY = 'timeOfDay';
    const NULLABLE_TIME_OF_DAY = 'nullableTimeOfDay';
    const TIMEZONED_DATE_TIME = 'timezonedDateTime';
    const NULLABLE_TIMEZONED_DATE_TIME = 'nullableTimezonedDateTime';

    /**
     * @var DateTime
     */
    public $dateTime;

    /**
     * @var DateTime|null
     */
    public $nullableDateTime;

    /**
     * @var Date
     */
    public $date;

    /**
     * @var Date|null
     */
    public $nullableDate;

    /**
     * @var TimeOfDay
     */
    public $timeOfDay;

    /**
     * @var TimeOfDay|null
     */
    public $nullableTimeOfDay;

    /**
     * @var TimezonedDateTime
     */
    public $timezonedDateTime;

    /**
     * @var TimezonedDateTime|null
     */
    public $nullableTimezonedDateTime;

    /**
     * Defines the structure of this class.
     *
     * @param ClassDefinition $class
     */
    protected function define(ClassDefinition $class)
    {
        $class->property($this->dateTime)->asObject(DateTime::class);

        $class->property($this->nullableDateTime)->nullable()->asObject(DateTime::class);

        $class->property($this->date)->asObject(Date::class);

        $class->property($this->nullableDate)->nullable()->asObject(Date::class);

        $class->property($this->timeOfDay)->asObject(TimeOfDay::class);

        $class->property($this->nullableTimeOfDay)->nullable()->asObject(TimeOfDay::class);

        $class->property($this->timezonedDateTime)->asObject(TimezonedDateTime::class);

        $class->property($this->nullableTimezonedDateTime)->nullable()->asObject(TimezonedDateTime::class);
    }
}