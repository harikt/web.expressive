<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Scaffold\Domain;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Model\IEntity;
use Dms\Core\Model\IValueObject;
use Dms\Core\Model\Object\Entity;
use Dms\Core\Model\Object\Enum;
use Dms\Core\Model\Object\FinalizedClassDefinition;
use Dms\Core\Model\Object\ValueObject;


/**
 * The domain object structure class
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DomainObjectStructure
{
    /**
     * @var FinalizedClassDefinition
     */
    protected $class;

    /**
     * @var DomainObjectRelation[]
     */
    protected $relations = [];

    /**
     * @var DomainObjectStructure[]
     */
    protected $subclasses = [];

    /**
     * DomainObjectStructure constructor.
     *
     * @param FinalizedClassDefinition $class
     */
    public function __construct(FinalizedClassDefinition $class)
    {
        $this->class = $class;
    }

    /**
     * @return FinalizedClassDefinition
     */
    public function getDefinition() : FinalizedClassDefinition
    {
        return $this->class;
    }

    /**
     * @return DomainObjectStructure[]
     */
    public function getSubclasses(): array
    {
        return $this->subclasses;
    }

    /**
     * @return DomainObjectRelation[]
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

    /**
     * @return bool
     */
    public function isEntity() : bool
    {
        return is_subclass_of($this->class->getClassName(), IEntity::class);
    }

    /**
     * @return bool
     */
    public function isRootEntity() : bool
    {
        return get_parent_class($this->class->getClassName()) === Entity::class;
    }

    /**
     * @return bool
     */
    public function isValueObject() : bool
    {
        return is_subclass_of($this->class->getClassName(), IValueObject::class);
    }

    /**
     * @return bool
     */
    public function isRootValueObject() : bool
    {
        return get_parent_class($this->class->getClassName()) === ValueObject::class;
    }

    /**
     * @return bool
     */
    public function isEnum() : bool
    {
        return is_subclass_of($this->class->getClassName(), Enum::class);
    }

    /**
     * @param string $propertyName
     *
     * @return bool
     */
    public function isRelation(string $propertyName) : bool
    {
        try {
            $this->getRelation($propertyName);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param string $propertyName
     *
     * @return DomainObjectRelation
     * @throws InvalidArgumentException
     */
    public function getRelation(string $propertyName) : DomainObjectRelation
    {
        foreach ($this->relations as $relation) {
            if ($relation->getDefinition()->getName() === $propertyName) {
                return $relation;
            }
        }

        throw InvalidArgumentException::format('Invalid property name: property does not map to a relation');
    }

    /**
     * @param DomainObjectStructure $subclass
     */
    public function addSubclass(DomainObjectStructure $subclass)
    {
        $this->subclasses[] = $subclass;
    }

    /**
     * @param DomainObjectRelation $relation
     */
    public function addRelation(DomainObjectRelation $relation)
    {
        $this->relations[] = $relation;
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    public function hasRelationOfType(string $className) : bool
    {
        try {
            $this->getRelationOfType($className);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param string $className
     *
     * @return DomainObjectRelation
     * @throws InvalidArgumentException
     */
    public function getRelationOfType(string $className) : DomainObjectRelation
    {
        foreach ($this->relations as $relation) {
            if ($relation->getRelatedObject()->getDefinition()->getClassName() === $className) {
                return $relation;
            }
        }

        throw InvalidArgumentException::format('No relation of type \'%s\' found', $className);
    }

    /**
     * @return \ReflectionClass
     */
    public function getReflection() : \ReflectionClass
    {
        return new \ReflectionClass($this->getDefinition()->getClassName());
    }

    /**
     * @return bool
     */
    public function hasEntityRelations() : bool
    {
        foreach ($this->relations as $relation) {
            if ($relation->getRelatedObject()->isEntity() || $relation->getRelatedObject()->hasEntityRelations()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $propertyName
     *
     * @return string
     */
    public function getPropertyReference(string $propertyName) : string
    {
        $constants = $this->getReflection()->getConstants();

        $constantName = array_search($propertyName, $constants, true);

        if ($constantName !== false) {
            return $this->getReflection()->getShortName() . '::' . $constantName;
        }

        return '\'' . $propertyName . '\'';
    }
}