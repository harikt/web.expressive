<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Scaffold\CodeGeneration;

use Dms\Common\Structure\DateTime\Date;
use Dms\Common\Structure\DateTime\DateOrTimeObject;
use Dms\Common\Structure\DateTime\DateTime;
use Dms\Common\Structure\DateTime\Persistence\DateMapper;
use Dms\Common\Structure\DateTime\Persistence\DateTimeMapper;
use Dms\Common\Structure\DateTime\Persistence\TimeOfDayMapper;
use Dms\Common\Structure\DateTime\Persistence\TimezonedDateTimeMapper;
use Dms\Common\Structure\DateTime\TimeOfDay;
use Dms\Common\Structure\DateTime\TimezonedDateTime;
use Dms\Core\Model\Object\FinalizedPropertyDefinition;
use Dms\Web\Expressive\Scaffold\Domain\DomainObjectStructure;
use Dms\Web\Expressive\Scaffold\ScaffoldCmsContext;
use Dms\Web\Expressive\Scaffold\ScaffoldPersistenceContext;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DateAndTimePropertyCodeGenerator extends CommonValueObjectPropertyCodeGenerator
{
    /**
     * @return string[]
     */
    protected function getSupportedValueObjectClasses() : array
    {
        return [DateOrTimeObject::class];
    }

    protected function doGeneratePersistenceMappingObjectMapperCode(
        ScaffoldPersistenceContext $context,
        PhpCodeBuilderContext $code,
        DomainObjectStructure $object,
        FinalizedPropertyDefinition $property,
        bool $isCollection,
        string $objectClass,
        string $columnName
    ) {
        if ($objectClass === DateTime::class) {
            $class = DateTimeMapper::class;
        } elseif ($objectClass === Date::class) {
            $class = DateMapper::class;
        } elseif ($objectClass === TimeOfDay::class) {
            $class = TimeOfDayMapper::class;
        } elseif ($objectClass === TimezonedDateTime::class) {
            $class = TimezonedDateTimeMapper::class;

            $code->addNamespaceImport($class);

            if ($property->getType()->isNullable()) {
                $code->getCode()->appendLine('->withIssetColumn(\'' . $columnName . '_date_time\')');
            }

            $code->getCode()->append('->using(new ' . $this->getShortClassName($class) . '(\'' . $columnName . '_date_time\', \'' . $columnName . '_timezone\'))');

            return;
        }

        $code->addNamespaceImport($class);

        if ($property->getType()->isNullable()) {
            $code->getCode()->appendLine('->withIssetColumn(\'' . $columnName . '\')');
        }

        $code->getCode()->append('->using(new ' . $this->getShortClassName($class) . '(\'' . $columnName . '\'))');
    }

    protected function doGenerateCmsObjectFieldCode(
        ScaffoldCmsContext $context,
        PhpCodeBuilderContext $code,
        DomainObjectStructure $object,
        FinalizedPropertyDefinition $property,
        bool $isCollection,
        string $objectClass
    ) {
        if ($objectClass === DateTime::class) {
            $code->getCode()->append('->dateTime()');
        } elseif ($objectClass === Date::class) {
            $code->getCode()->append('->date()');
        } elseif ($objectClass === TimeOfDay::class) {
            $code->getCode()->append('->time()');
        } elseif ($objectClass === TimezonedDateTime::class) {
            $code->getCode()->append('->dateTimeWithTimezone()');
        }
    }
}
