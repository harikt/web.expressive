<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Persistence\Infrastructure;

use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\IndependentValueObjectMapper;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestDateTimeValueObject;
use Dms\Common\Structure\DateTime\Persistence\DateTimeMapper;
use Dms\Common\Structure\DateTime\Persistence\DateMapper;
use Dms\Common\Structure\DateTime\Persistence\TimeOfDayMapper;
use Dms\Common\Structure\DateTime\Persistence\TimezonedDateTimeMapper;

/**
 * The Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestDateTimeValueObject value object mapper.
 */
class TestDateTimeValueObjectMapper extends IndependentValueObjectMapper
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
        $map->type(TestDateTimeValueObject::class);

        $map->embeddedCollection(TestDateTimeValueObject::DATE_TIME)
            ->toTable('test_date_time_value_object_date_time')
            ->withPrimaryKey('id')
            ->withForeignKeyToParentAs('test_date_time_value_object_id')
            ->using(new DateTimeMapper('date_time'));

        $map->embeddedCollection(TestDateTimeValueObject::DATE)
            ->toTable('test_date_time_value_object_date')
            ->withPrimaryKey('id')
            ->withForeignKeyToParentAs('test_date_time_value_object_id')
            ->using(new DateMapper('date'));

        $map->embeddedCollection(TestDateTimeValueObject::TIME_OF_DAY)
            ->toTable('test_date_time_value_object_time_of_day')
            ->withPrimaryKey('id')
            ->withForeignKeyToParentAs('test_date_time_value_object_id')
            ->using(new TimeOfDayMapper('time_of_day'));

        $map->embeddedCollection(TestDateTimeValueObject::TIMEZONED_DATE_TIME)
            ->toTable('test_date_time_value_object_timezoned_date_time')
            ->withPrimaryKey('id')
            ->withForeignKeyToParentAs('test_date_time_value_object_id')
            ->using(new TimezonedDateTimeMapper('timezoned_date_time_date_time', 'timezoned_date_time_timezone'));


    }
}