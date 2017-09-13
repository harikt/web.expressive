<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObject\Domain;

use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\ValueObject;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TestValueObject extends ValueObject
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
     * Defines the structure of this class.
     *
     * @param ClassDefinition $class
     */
    protected function define(ClassDefinition $class)
    {
        $class->property($this->string)->asString();

        $class->property($this->int)->asInt();

        $class->property($this->float)->asFloat();

        $class->property($this->bool)->asBool();
    }
}