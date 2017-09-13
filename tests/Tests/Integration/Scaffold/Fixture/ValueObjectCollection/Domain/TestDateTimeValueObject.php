<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain;

use Dms\Common\Structure\DateTime\Date;
use Dms\Common\Structure\DateTime\DateTime;
use Dms\Common\Structure\DateTime\TimeOfDay;
use Dms\Common\Structure\DateTime\TimezonedDateTime;
use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\ValueObject;
use Dms\Core\Model\ValueObjectCollection;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TestDateTimeValueObject extends ValueObject
{
    const DATE_TIME = 'dateTime';
    const DATE = 'date';
    const TIME_OF_DAY = 'timeOfDay';
    const TIMEZONED_DATE_TIME = 'timezonedDateTime';

    /**
     * @var ValueObjectCollection|DateTime[]
     */
    public $dateTime;

    /**
     * @var ValueObjectCollection|Date[]
     */
    public $date;

    /**
     * @var ValueObjectCollection|TimeOfDay[]
     */
    public $timeOfDay;

    /**
     * @var ValueObjectCollection|TimezonedDateTime[]
     */
    public $timezonedDateTime;


    /**
     * Defines the structure of this class.
     *
     * @param ClassDefinition $class
     */
    protected function define(ClassDefinition $class)
    {
        $class->property($this->dateTime)->asType(DateTime::collectionType());

        $class->property($this->date)->asType(Date::collectionType());

        $class->property($this->timeOfDay)->asType(TimeOfDay::collectionType());

        $class->property($this->timezonedDateTime)->asType(TimezonedDateTime::collectionType());
    }
}