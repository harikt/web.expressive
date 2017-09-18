<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Scaffold\CodeGeneration\Relation;

use Dms\Core\Model\Object\FinalizedPropertyDefinition;
use Dms\Core\Model\Object\ValueObject;
use Dms\Core\Model\Type\ObjectType;
use Dms\Web\Expressive\Scaffold\CodeGeneration\PhpCodeBuilderContext;
use Dms\Web\Expressive\Scaffold\CodeGeneration\PropertyCodeGenerator;
use Dms\Web\Expressive\Scaffold\Domain\DomainObjectStructure;
use Dms\Web\Expressive\Scaffold\Domain\DomainStructure;
use Dms\Web\Expressive\Scaffold\ScaffoldCmsContext;
use Dms\Web\Expressive\Scaffold\ScaffoldPersistenceContext;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class CustomValueObjectPropertyCodeGenerator extends PropertyCodeGenerator
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
        return $property->getType()->nonNullable()->isSubsetOf(ValueObject::type())
            && $domain->hasObject($property->getType()->nonNullable()->asTypeString());
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
        $code->getCode()->appendLine('$map->embedded(' . $propertyReference . ')');

        $code->getCode()->indent++;

        /** @var ObjectType $type */
        $type        = $property->getType()->nonNullable();
        $valueObject = $context->getDomainStructure()->getObject($type->getClass());

        if ($property->getType()->isNullable()) {
            $code->getCode()->appendLine('->withIssetColumn(\'has_' . $columnName . '\')');
        }

        if ($this->hasMultipleValueObjectsOfType($object, $valueObject->getDefinition()->getClassName())) {
            $code->getCode()->appendLine('->withColumnsPrefixedBy(\'' . $columnName . '_\')');
        }

        if ($valueObject->hasEntityRelations()) {
            $code->addNamespaceImport($valueObject->getDefinition()->getClassName());
            $code->getCode()->append('->to(' . $valueObject->getReflection()->getShortName() . '::class)');
        } else {
            $relativeNamespace = $context->getRelativeObjectNamespace($valueObject);
            $mapperNamespace = $context->getOutputImplementationNamespace() . ($relativeNamespace ? '\\' . $relativeNamespace : '');
            $mapperClass     = $mapperNamespace . '\\' . $valueObject->getReflection()->getShortName() . 'Mapper';

            $code->addNamespaceImport($mapperClass);
            $code->getCode()->append('->using(new ' . $this->getShortClassName($mapperClass) . '())');
        }

        $code->getCode()->indent--;
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
        $isRequired = !$property->getType()->isNullable();

        /** @var ObjectType $type */
        $type        = $property->getType()->nonNullable();
        $valueObject = $context->getDomainStructure()->getObject($type->getClass());

        $relativeNamespace = $context->getRelativeObjectNamespace($valueObject);
        $fieldNamespace    = $context->getValueObjectFieldNamespace() . ($relativeNamespace ? '\\' . $relativeNamespace : '');
        $fieldClass        = $fieldNamespace . '\\' . $valueObject->getReflection()->getShortName() . 'Field';

        $code->addNamespaceImport($fieldClass);

        if ($isRequired) {
            $code->getCode()->append('(');
        }

        $code->getCode()->append('new ' . $this->getShortClassName($fieldClass) . '(\'' . $fieldName . '\', \'' . $fieldLabel . '\')');

        if ($isRequired) {
            $code->getCode()->append(')->required()');
        }
    }

    private function hasMultipleValueObjectsOfType(DomainObjectStructure $object, string $valueObjectClass) : bool
    {
        $count = 0;

        foreach ($object->getDefinition()->getProperties() as $property) {
            if ($property->getType()->nullable()->isSubsetOf($valueObjectClass::type())) {
                $count++;
            }
        }

        return $count > 1;
    }
}
