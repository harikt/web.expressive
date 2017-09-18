<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Scaffold\CodeGeneration\Relation;

use Dms\Core\Model\Object\FinalizedPropertyDefinition;
use Dms\Core\Model\Object\ValueObject;
use Dms\Core\Model\Type\CollectionType;
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
class CustomValueObjectCollectionPropertyCodeGenerator extends PropertyCodeGenerator
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
        return $property->getType()->isSubsetOf(ValueObject::collectionType());
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
        $code->getCode()->appendLine('$map->embeddedCollection(' . $propertyReference . ')');

        $code->getCode()->indent++;

        /** @var CollectionType $type */
        $type = $property->getType();
        /** @var ObjectType $elementType */
        $elementType = $type->getElementType();
        $valueObject = $context->getDomainStructure()->getObject($elementType->getClass());

        $tableName      = snake_case($object->getReflection()->getShortName()) . '_' . str_plural(snake_case($property->getName()));
        $primaryKeyName = 'id';
        $foreignKeyName = snake_case($object->getReflection()->getShortName()) . '_id';

        $code->getCode()->appendLine('->toTable(\'' . $tableName . '\')');
        $code->getCode()->appendLine('->withPrimaryKey(\'' . $primaryKeyName . '\')');
        $code->getCode()->appendLine('->withForeignKeyToParentAs(\'' . $foreignKeyName . '\')');

        if ($valueObject->hasEntityRelations()) {
            $code->addNamespaceImport($valueObject->getDefinition()->getClassName());
            $code->getCode()->append('->to(' . $valueObject->getReflection()->getShortName() . '::class)');
        } else {
            $relativeNamespace = $context->getRelativeObjectNamespace($valueObject);
            $mapperNamespace   = $context->getOutputImplementationNamespace() . ($relativeNamespace ? '\\' . $relativeNamespace : '');
            $mapperClass       = $mapperNamespace . '\\' . $valueObject->getReflection()->getShortName() . 'Mapper';

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
        /** @var CollectionType $type */
        $type = $property->getType();
        /** @var ObjectType $elementType */
        $elementType = $type->getElementType();
        $valueObject = $context->getDomainStructure()->getObject($elementType->getClass());

        $relativeNamespace = $context->getRelativeObjectNamespace($valueObject);
        $fieldNamespace    = $context->getValueObjectFieldNamespace() . ($relativeNamespace ? '\\' . $relativeNamespace : '');
        $fieldClass        = $fieldNamespace . '\\' . $valueObject->getReflection()->getShortName() . 'Field';

        $code->addNamespaceImport($valueObject->getDefinition()->getClassName());
        $code->addNamespaceImport($fieldClass);

        $code->getCode()->appendLine('Field::create(\'' . $fieldName . '\', \'' . $fieldLabel . '\')->arrayOfField(');

        $code->getCode()->indent++;

        $code->getCode()->appendLine('new ' . $this->getShortClassName($fieldClass) . '(\'' . $fieldName . '\', \'' . $fieldLabel . '\')');

        $code->getCode()->indent--;

        $code->getCode()->append(')->mapToCollection(' . $valueObject->getReflection()->getShortName() . '::collectionType())');
    }
}
