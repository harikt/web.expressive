<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Persistence\Infrastructure;

use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\IndependentValueObjectMapper;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestMoneyValueObject;
use Dms\Common\Structure\Money\Persistence\MoneyMapper;

/**
 * The Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestMoneyValueObject value object mapper.
 */
class TestMoneyValueObjectMapper extends IndependentValueObjectMapper
{
    /**
     * Defines the entity mapper
     *
     * @param MapperDefinition $map
     *
     * @return void
     */
    protected function define(MapperDefinition $map)
    {
        $map->type(TestMoneyValueObject::class);

        $map->embeddedCollection(TestMoneyValueObject::MONEY)
            ->toTable('test_money_value_object_money')
            ->withPrimaryKey('id')
            ->withForeignKeyToParentAs('test_money_value_object_id')
            ->using(new MoneyMapper('money_amount', 'money_currency'));
    }
}
