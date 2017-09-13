<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\Enum\Domain;

use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\ValueObject;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TestValueObjectWithEnum extends ValueObject
{
    const ENUM = 'enum';
    const NULLABLE_ENUM = 'nullableEnum';

    /**
     * @var TestEnum
     */
    public $enum;

    /**
     * @var TestEnum
     */
    public $nullableEnum;

    /**
     * Defines the structure of this class.
     *
     * @param ClassDefinition $class
     */
    protected function define(ClassDefinition $class)
    {
        $class->property($this->enum)->asObject(TestEnum::class);

        $class->property($this->nullableEnum)->nullable()->asObject(TestEnum::class);
    }
}