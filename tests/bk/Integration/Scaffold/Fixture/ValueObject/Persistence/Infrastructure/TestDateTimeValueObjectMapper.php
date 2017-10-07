<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Persistence\Infrastructure;

use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\IndependentValueObjectMapper;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestDateTimeValueObject;
use Dms\Common\Structure\DateTime\Persistence\DateTimeMapper;
use Dms\Common\Structure\DateTime\Persistence\DateMapper;
use Dms\Common\Structure\DateTime\Persistence\TimeOfDayMapper;
use Dms\Common\Structure\DateTime\Persistence\TimezonedDateTimeMapper;

/**
 * The Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestDateTimeValueObject value object mapper.
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

        $map->embedded(TestDateTimeValueObject::DATE_TIME)
            ->using(new DateTimeMapper('date_time'));

        $map->embedded(TestDateTimeValueObject::NULLABLE_DATE_TIME)
            ->withIssetColumn('nullable_date_time')
            ->using(new DateTimeMapper('nullable_date_time'));

        $map->embedded(TestDateTimeValueObject::DATE)
            ->using(new DateMapper('date'));

        $map->embedded(TestDateTimeValueObject::NULLABLE_DATE)
            ->withIssetColumn('nullable_date')
            ->using(new DateMapper('nullable_date'));

        $map->embedded(TestDateTimeValueObject::TIME_OF_DAY)
            ->using(new TimeOfDayMapper('time_of_day'));

        $map->embedded(TestDateTimeValueObject::NULLABLE_TIME_OF_DAY)
            ->withIssetColumn('nullable_time_of_day')
            ->using(new TimeOfDayMapper('nullable_time_of_day'));

        $map->embedded(TestDateTimeValueObject::TIMEZONED_DATE_TIME)
            ->using(new TimezonedDateTimeMapper('timezoned_date_time_date_time', 'timezoned_date_time_timezone'));

        $map->embedded(TestDateTimeValueObject::NULLABLE_TIMEZONED_DATE_TIME)
            ->withIssetColumn('nullable_timezoned_date_time_date_time')
            ->using(new TimezonedDateTimeMapper('nullable_timezoned_date_time_date_time', 'nullable_timezoned_date_time_timezone'));
    }
}
