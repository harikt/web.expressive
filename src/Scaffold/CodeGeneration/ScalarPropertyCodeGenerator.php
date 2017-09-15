<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Scaffold\CodeGeneration;

use Dms\Core\Model\Object\FinalizedPropertyDefinition;
use Dms\Core\Model\Type\Builder\Type;
use Dms\Core\Model\Type\ScalarType;
use Dms\Web\Expressive\Scaffold\Domain\DomainObjectStructure;
use Dms\Web\Expressive\Scaffold\Domain\DomainStructure;
use Dms\Web\Expressive\Scaffold\ScaffoldCmsContext;
use Dms\Web\Expressive\Scaffold\ScaffoldPersistenceContext;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ScalarPropertyCodeGenerator extends PropertyCodeGenerator
{
    /**
     * @param DomainStructure             $domain
     * @param DomainObjectStructure       $object
     * @param FinalizedPropertyDefinition $property
     *
     * @return bool
     */
    protected function doesSupportProperty(DomainStructure $domain, DomainObjectStructure $object, FinalizedPropertyDefinition $property) : bool
    {
        return $property->getType()->nonNullable() instanceof ScalarType;
    }


    /**
     * @param ScaffoldPersistenceContext  $context
     * @param PhpCodeBuilderContext       $code
     * @param DomainObjectStructure       $object
     * @param FinalizedPropertyDefinition $property
     * @param string                      $propertyReference
     * @param string                      $columnName
     */
    protected function doGeneratePersistenceMappingCode(
        ScaffoldPersistenceContext $context,
        PhpCodeBuilderContext $code,
        DomainObjectStructure $object,
        FinalizedPropertyDefinition $property,
        string $propertyReference,
        string $columnName
    ) {
        $php = '$map->property(' . $propertyReference . ')->to(\'' . $columnName . '\')';

        if ($property->getType()->isNullable()) {
            $php .= '->nullable()';
        }

        /** @var ScalarType $type */
        $type = $property->getType()->nonNullable();

        if ($type->equals(Type::string())) {
            $php .= '->asVarchar(255)';
        } elseif ($type->equals(Type::int())) {
            $php .= '->asInt()';
        } elseif ($type->equals(Type::float())) {
            $php .= '->asDecimal(16, 8)';
        } elseif ($type->equals(Type::bool())) {
            $php .= '->asBool()';
        }

        $code->getCode()->append($php);
    }

    /**
     * @param ScaffoldCmsContext          $context
     * @param PhpCodeBuilderContext       $code
     * @param DomainObjectStructure       $object
     * @param FinalizedPropertyDefinition $property
     * @param string                      $propertyReference
     * @param string                      $fieldName
     * @param string                      $fieldLabel
     */
    protected function doGenerateCmsFieldCode(
        ScaffoldCmsContext $context,
        PhpCodeBuilderContext $code,
        DomainObjectStructure $object,
        FinalizedPropertyDefinition $property,
        string $propertyReference,
        string $fieldName,
        string $fieldLabel
    ) {
        $php = 'Field::create(\'' . $fieldName . '\', \'' . $fieldLabel . '\')';

        /** @var ScalarType $type */
        $type = $property->getType()->nonNullable();

        if ($type->equals(Type::string())) {
            $php .= '->string()';
        } elseif ($type->equals(Type::int())) {
            $php .= '->int()';
        } elseif ($type->equals(Type::float())) {
            $php .= '->decimal()';
        } elseif ($type->equals(Type::bool())) {
            $php .= '->bool()';
        }

        if (!$property->getType()->isNullable() && !$type->equals(Type::bool())) {
            $php .= '->required()';
        }

        $code->getCode()->append($php);
    }
}