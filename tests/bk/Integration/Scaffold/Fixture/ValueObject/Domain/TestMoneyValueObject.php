<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Domain;

use Dms\Common\Structure\Money\Currency;
use Dms\Common\Structure\Money\Money;
use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\ValueObject;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TestMoneyValueObject extends ValueObject
{
    const CURRENCY = 'currency';
    const NULLABLE_CURRENCY = 'nullableCurrency';
    const MONEY = 'money';
    const NULLABLE_MONEY = 'nullableMoney';

    /**
     * @var Currency
     */
    public $currency;

    /**
     * @var Currency|null
     */
    public $nullableCurrency;

    /**
     * @var Money
     */
    public $money;

    /**
     * @var Money|null
     */
    public $nullableMoney;

    /**
     * Defines the structure of this class.
     *
     * @param ClassDefinition $class
     */
    protected function define(ClassDefinition $class)
    {
        $class->property($this->currency)->asObject(Currency::class);

        $class->property($this->nullableCurrency)->nullable()->asObject(Currency::class);

        $class->property($this->money)->asObject(Money::class);

        $class->property($this->nullableMoney)->nullable()->asObject(Money::class);
    }
}
