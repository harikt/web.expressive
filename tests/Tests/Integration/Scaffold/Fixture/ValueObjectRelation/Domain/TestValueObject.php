<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectRelation\Domain;

use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\ValueObject;


/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TestValueObject extends ValueObject
{
    const STRING = 'string';

    /**
     * @var string
     */
    public $string;

    /**
     * Defines the structure of this class.
     *
     * @param ClassDefinition $class
     */
    protected function define(ClassDefinition $class)
    {
        $class->property($this->string)->asString();
    }
}