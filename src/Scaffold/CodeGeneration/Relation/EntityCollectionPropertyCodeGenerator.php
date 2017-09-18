<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Scaffold\CodeGeneration\Relation;

use Dms\Core\Model\Object\Entity;
use Dms\Core\Model\Object\FinalizedPropertyDefinition;
use Dms\Web\Expressive\Scaffold\CodeGeneration\PhpCodeBuilderContext;
use Dms\Web\Expressive\Scaffold\CodeGeneration\PropertyCodeGenerator;
use Dms\Web\Expressive\Scaffold\Domain\DomainObjectRelation;
use Dms\Web\Expressive\Scaffold\Domain\DomainObjectStructure;
use Dms\Web\Expressive\Scaffold\Domain\DomainStructure;
use Dms\Web\Expressive\Scaffold\ScaffoldCmsContext;
use Dms\Web\Expressive\Scaffold\ScaffoldPersistenceContext;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class EntityCollectionPropertyCodeGenerator extends PropertyCodeGenerator
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
        return $property->getType()->nonNullable()->isSubsetOf(Entity::collectionType());
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
        $relation = $object->getRelation($property->getName());

        $isManyToMany = $relation->hasInverseRelation() && $relation->getInverseRelation()->isToMany();

        $entity = $context->getDomainStructure()->getObject($relation->getRelatedObject()->getDefinition()->getClassName());

        $code->addNamespaceImport($entity->getDefinition()->getClassName());

        $code->getCode()->appendLine('$map->relation(' . $propertyReference . ')');

        $code->getCode()->indent++;

        $code->getCode()->appendLine('->to(' . $entity->getReflection()->getShortName() . '::class)');

        if ($isManyToMany) {
            $code->getCode()->appendLine('->toMany()');
        } else {
            $code->getCode()->appendLine('->toMany()');

            $isIdentifying = $relation->hasInverseRelation() && !$relation->getInverseRelation()->getDefinition()->getType()->isNullable();

            if ($isIdentifying) {
                $code->getCode()->appendLine('->identifying()');
            }
        }

        if ($relation->hasInverseRelation()) {
            $inverseReference = $this->getPropertyReference(
                $relation->getRelatedObject(),
                $relation->getInverseRelation()->getDefinition()->getName()
            );

            $code->getCode()->appendLine('->withBidirectionalRelation(' . $inverseReference . ')');
        }

        if ($isManyToMany) {
            $parentForeignKeyName  = snake_case($object->getReflection()->getShortName()) . '_id';
            $relatedForeignKeyName = snake_case($entity->getReflection()->getShortName()) . '_id';
            $code->getCode()->appendLine('->throughJoinTable(\'' . $this->getJoinTableName($object, $relation) . '\')');
            $code->getCode()->appendLine('->withParentIdAs(\'' . $parentForeignKeyName . '\')');
            $code->getCode()->append('->withRelatedIdAs(\'' . $relatedForeignKeyName . '\')');
        } else {
            $parentForeignKeyName = snake_case($object->getReflection()->getShortName()) . '_id';
            $code->getCode()->append('->withParentIdAs(\'' . $parentForeignKeyName . '\')');
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
        $relation = $object->getRelation($property->getName());

        $entity = $context->getDomainStructure()->getObject($relation->getRelatedObject()->getDefinition()->getClassName());

        $relativeNamespace      = $context->getRelativeObjectNamespace($entity);
        $dataSourceNamespace    = $context->getDataSourceNamespace() . ($relativeNamespace ? '\\' . $relativeNamespace : '');
        $dataSourceInterface    = $dataSourceNamespace . '\\I' . $entity->getReflection()->getShortName() . 'Repository';
        $dataSourcePropertyName = camel_case($entity->getReflection()->getShortName()) . 'Repository';

        $code->addNamespaceImport($dataSourceInterface);
        $code->addNamespaceImport($entity->getDefinition()->getClassName());
        $dataSourcePropertyName = $code->addConstructorParameter($this->getShortClassName($dataSourceInterface), $dataSourcePropertyName);

        $code->getCode()->appendLine('Field::create(\'' . $fieldName . '\', \'' . $fieldLabel . '\')');
        $code->getCode()->indent++;

        $code->getCode()->appendLine('->entitiesFrom($this->' . $dataSourcePropertyName . ')');

        $code->getCode()->appendLine('->labelledBy(' . $this->findLabelProperty($entity) . ')');

        $code->getCode()->append('->mapToCollection(' . $entity->getReflection()->getShortName() . '::collectionType())');

        $code->getCode()->indent--;
    }

    protected function findLabelProperty(DomainObjectStructure $entity) : string
    {
        foreach ($entity->getDefinition()->getProperties() as $property) {
            if (str_contains(strtolower($property->getName()), ['name', 'title'])) {
                return $this->getPropertyReference($entity, $property->getName());
            }
        }

        return '/* FIXME: */ ' . $this->getPropertyReference($entity, 'id');
    }

    private function getJoinTableName(DomainObjectStructure $parentEntity, DomainObjectRelation $relation) : string
    {
        $tables = [
            snake_case($parentEntity->getReflection()->getShortName()),
            snake_case($relation->getRelatedObject()->getReflection()->getShortName()),
        ];

        // Ensure consistent order
        sort($tables, SORT_STRING);

        return $tables[0] . '_' . str_plural($tables[1]);
    }
}
