<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\Simple\Domain;

use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\Entity;


/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TestEntity extends Entity
{
    const STRING = 'string';
    const INT = 'int';
    const FLOAT = 'float';
    const BOOL = 'bool';

    /**
     * @var string
     */
    public $string;

    /**
     * @var int
     */
    public $int;

    /**
     * @var float
     */
    public $float;

    /**
     * @var bool
     */
    public $bool;

    /**
     * Defines the structure of this entity.
     *
     * @param ClassDefinition $class
     */
    protected function defineEntity(ClassDefinition $class)
    {
        $class->property($this->string)->asString();

        $class->property($this->int)->asInt();

        $class->property($this->float)->asFloat();

        $class->property($this->bool)->asBool();
    }
}