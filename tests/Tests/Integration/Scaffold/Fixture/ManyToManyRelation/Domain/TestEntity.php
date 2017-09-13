<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Domain;

use Dms\Core\Model\EntityCollection;
use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\Entity;


/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TestEntity extends Entity
{
    const RELATED = 'related';

    /**
     * @var EntityCollection|TestEntity[]
     */
    public $related;

    /**
     * Defines the structure of this entity.
     *
     * @param ClassDefinition $class
     */
    protected function defineEntity(ClassDefinition $class)
    {
        $class->property($this->related)->asType(TestRelatedEntity::collectionType());
    }
}