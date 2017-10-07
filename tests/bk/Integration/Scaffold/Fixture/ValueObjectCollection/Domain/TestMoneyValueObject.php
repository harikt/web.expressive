<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain;

use Dms\Common\Structure\Money\Currency;
use Dms\Common\Structure\Money\Money;
use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\ValueObject;
use Dms\Core\Model\ValueObjectCollection;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TestMoneyValueObject extends ValueObject
{
    const MONEY = 'money';

    /**
     * @var ValueObjectCollection|Money[]
     */
    public $money;

    /**
     * Defines the structure of this class.
     *
     * @param ClassDefinition $class
     */
    protected function define(ClassDefinition $class)
    {
        $class->property($this->money)->asType(Money::collectionType());
    }
}
