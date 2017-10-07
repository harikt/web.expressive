<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectRelation\Domain;

use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\Entity;
use Dms\Core\Model\ValueObjectCollection;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TestEntity extends Entity
{
    const VALUE_OBJECT = 'valueObject';
    const NULLABLE_VALUE_OBJECT = 'nullableValueObject';
    const VALUE_OBJECT_COLLECTION = 'valueObjectCollection';

    /**
     * @var TestValueObject
     */
    public $valueObject;

    /**
     * @var TestValueObject|null
     */
    public $nullableValueObject;

    /**
     * @var ValueObjectCollection|TestValueObject[]
     */
    public $valueObjectCollection;

    /**
     * Defines the structure of this entity.
     *
     * @param ClassDefinition $class
     */
    protected function defineEntity(ClassDefinition $class)
    {
        $class->property($this->valueObject)->asObject(TestValueObject::class);

        $class->property($this->nullableValueObject)->nullable()->asObject(TestValueObject::class);

        $class->property($this->valueObjectCollection)->asType(TestValueObject::collectionType());
    }
}
