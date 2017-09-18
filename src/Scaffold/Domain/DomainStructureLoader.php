<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Scaffold\Domain;

use Dms\Core\Model\Object\TypedObject;
use Dms\Core\Model\Type\CollectionType;
use Dms\Core\Model\Type\ObjectType;
use Dms\Web\Expressive\Scaffold\NamespaceDirectoryResolver;
use Pinq\Traversable;
use Symfony\Component\Finder\Finder;

/**
 * The domain structure loader
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DomainStructureLoader
{
    /**
     * @var NamespaceDirectoryResolver
     */
    protected $namespaceDirectoryResolver;

    /**
     * DomainStructureLoader constructor.
     *
     * @param NamespaceDirectoryResolver $namespaceDirectoryResolver
     */
    public function __construct(NamespaceDirectoryResolver $namespaceDirectoryResolver)
    {
        $this->namespaceDirectoryResolver = $namespaceDirectoryResolver;
    }

    /**
     * @param string $namespace
     *
     * @return DomainStructure
     */
    public function loadDomainStructure(string $namespace) : DomainStructure
    {
        $this->loadAllApplicationClasses($namespace);

        /** @var string[]|TypedObject[] $classes */
        $classes = $this->loadClassNames($namespace);

        /** @var DomainObjectStructure[] $objectStructures */
        $objectStructures = $this->loadEmptyObjectStructures($classes);

        $this->loadSubclasses($objectStructures);
        $this->loadRelations($objectStructures);
        $this->detectInverseRelations($objectStructures);

        return new DomainStructure($objectStructures);
    }

    /**
     * @param string $namespace
     */
    protected function loadAllApplicationClasses(string $namespace)
    {
        $path = $this->namespaceDirectoryResolver->getDirectoryFor($namespace);

        foreach (Finder::create()->files()->name('*.php')->in($path) as $file) {
            /** @var \SplFileInfo $file */
            require_once $file->getRealPath();
        }
    }

    /**
     * @param string $namespace
     *
     * @return array
     */
    protected function loadClassNames(string $namespace) : array
    {
        return Traversable::from(get_declared_classes())
            ->where(function (string $class) use ($namespace) {
                return starts_with($class, $namespace) && is_subclass_of($class, TypedObject::class);
            })
            ->asArray();
    }

    /**
     * @param string[]|TypedObject[] $classes
     *
     * @return DomainObjectStructure[]
     */
    protected function loadEmptyObjectStructures($classes) : array
    {
        $objectStructures = [];

        foreach ($classes as $class) {
            $objectStructures[$class] = new DomainObjectStructure($class::definition());
        }

        return $objectStructures;
    }

    /**
     * @param DomainObjectStructure[] $objectStructures
     */
    protected function loadSubclasses(array $objectStructures)
    {
        foreach ($objectStructures as $objectStructure) {
            $parentClass = get_parent_class($objectStructure->getDefinition()->getClassName());

            if (isset($objectStructures[$parentClass])) {
                $objectStructures[$parentClass]->addSubclass($objectStructure);
            }
        }
    }

    /**
     * @param DomainObjectStructure[] $objectStructures
     */
    private function loadRelations(array $objectStructures)
    {
        foreach ($objectStructures as $objectStructure) {
            foreach ($objectStructure->getDefinition()->getProperties() as $property) {
                $propertyType = $property->getType()->nonNullable();

                if ($propertyType instanceof CollectionType) {
                    $relatedObjectType = $propertyType->getElementType();
                } else {
                    $relatedObjectType = $propertyType;
                }

                if (!($relatedObjectType instanceof ObjectType)) {
                    continue;
                }

                $relatedObjectClass = $relatedObjectType->getClass();

                if (!isset($objectStructures[$relatedObjectClass])) {
                    continue;
                }

                $relation = new DomainObjectRelation(
                    $propertyType instanceof CollectionType
                        ? DomainObjectRelationMode::toMany()
                        : DomainObjectRelationMode::toOne(),
                    $property,
                    $objectStructures[$relatedObjectClass]
                );

                $objectStructure->addRelation($relation);
            }
        }
    }

    /**
     * @param DomainObjectStructure[] $objectStructures
     */
    private function detectInverseRelations(array $objectStructures)
    {
        foreach ($objectStructures as $objectStructure) {
            foreach ($objectStructure->getRelations() as $relation) {
                $relatedClassName = $objectStructure->getDefinition()->getClassName();

                if ($relation->getRelatedObject()->hasRelationOfType($relatedClassName)) {
                    $inverseRelation = $relation->getRelatedObject()->getRelationOfType($relatedClassName);

                    $relation->setInverseRelation($inverseRelation);
                }
            }
        }
    }
}
