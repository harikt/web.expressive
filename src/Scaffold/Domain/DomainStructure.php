<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Scaffold\Domain;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Model\Traversable;


/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DomainStructure
{
    /**
     * @var DomainObjectStructure[]
     */
    protected $objects = [];

    /**
     * DomainStructure constructor.
     *
     * @param DomainObjectStructure[] $objects
     */
    public function __construct(array $objects)
    {
        InvalidArgumentException::verifyAllInstanceOf(__METHOD__, 'objects', $objects, DomainObjectStructure::class);

        $this->objects = array_values($objects);
    }

    /**
     * @return DomainObjectStructure[]
     */
    public function getObjects(): array
    {
        return $this->objects;
    }

    /**
     * @return DomainObjectStructure[]
     */
    public function getEntities(): array
    {
        return Traversable::from($this->objects)
            ->where(function (DomainObjectStructure $structure) {
                return $structure->isEntity();
            })
            ->asArray();
    }

    /**
     * @return DomainObjectStructure[]
     */
    public function getValueObjects(): array
    {
        return Traversable::from($this->objects)
            ->where(function (DomainObjectStructure $structure) {
                return $structure->isValueObject();
            })
            ->asArray();
    }

    /**
     * @return DomainObjectStructure[]
     */
    public function getRootEntities(): array
    {
        return Traversable::from($this->objects)
            ->where(function (DomainObjectStructure $structure) {
                return $structure->isRootEntity();
            })
            ->asArray();
    }

    /**
     * @return DomainObjectStructure[]
     */
    public function getRootValueObjects(): array
    {
        return Traversable::from($this->objects)
            ->where(function (DomainObjectStructure $structure) {
                return $structure->isRootValueObject();
            })
            ->asArray();
    }

    /**
     * @param string $class
     *
     * @return DomainObjectStructure
     * @throws InvalidArgumentException
     */
    public function getObject(string $class): DomainObjectStructure
    {
        foreach ($this->objects as $object) {
            if ($object->getDefinition()->getClassName() === $class) {
                return $object;
            }
        }

        throw InvalidArgumentException::format('Class not found: %s', $class);
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function hasObject(string $class): bool
    {
        try {
            $this->getObject($class);
            return true;
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }
}