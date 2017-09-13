<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObject\Cms\Modules\Fields;

use Dms\Core\Common\Crud\Definition\Form\ValueObjectFieldDefinition;
use Dms\Core\Common\Crud\Form\ValueObjectField;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestMoneyValueObject;
use Dms\Common\Structure\Field;
use Dms\Common\Structure\Money\Currency;

/**
 * The Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestMoneyValueObject value object field.
 */
class TestMoneyValueObjectField extends ValueObjectField
{


    public function __construct(string $name, string $label)
    {

        parent::__construct($name, $label);
    }

    /**
     * Defines the structure of this value object field.
     *
     * @param ValueObjectFieldDefinition $form
     *
     * @return void
     */
    protected function define(ValueObjectFieldDefinition $form)
    {
        $form->bindTo(TestMoneyValueObject::class);

        $form->section('Details', [
            $form->field(
                Field::create('currency', 'Currency')->enum(Currency::class, Currency::getNameMap())->required()
            )->bindToProperty(TestMoneyValueObject::CURRENCY),
            //
            $form->field(
                Field::create('nullable_currency', 'Nullable Currency')->enum(Currency::class, Currency::getNameMap())
            )->bindToProperty(TestMoneyValueObject::NULLABLE_CURRENCY),
            //
            $form->field(
                Field::create('money', 'Money')->money()->required()
            )->bindToProperty(TestMoneyValueObject::MONEY),
            //
            $form->field(
                Field::create('nullable_money', 'Nullable Money')->money()
            )->bindToProperty(TestMoneyValueObject::NULLABLE_MONEY),
            //
        ]);

    }
}