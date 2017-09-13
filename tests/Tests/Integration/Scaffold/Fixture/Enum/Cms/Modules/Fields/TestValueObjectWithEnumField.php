<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\Enum\Cms\Modules\Fields;

use Dms\Core\Common\Crud\Definition\Form\ValueObjectFieldDefinition;
use Dms\Core\Common\Crud\Form\ValueObjectField;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\Enum\Domain\TestValueObjectWithEnum;
use Dms\Common\Structure\Field;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\Enum\Domain\TestEnum;

/**
 * The Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\Enum\Domain\TestValueObjectWithEnum value object field.
 */
class TestValueObjectWithEnumField extends ValueObjectField
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
        $form->bindTo(TestValueObjectWithEnum::class);

        $form->section('Details', [
            $form->field(
                Field::create('enum', 'Enum')->enum(TestEnum::class, [
                    TestEnum::STRING => 'String',
                    TestEnum::INT => 'Int',
                    TestEnum::FLOAT => 'Float',
                    TestEnum::BOOL => 'Bool',
                ])->required()
            )->bindToProperty(TestValueObjectWithEnum::ENUM),
            //
            $form->field(
                Field::create('nullable_enum', 'Nullable Enum')->enum(TestEnum::class, [
                    TestEnum::STRING => 'String',
                    TestEnum::INT => 'Int',
                    TestEnum::FLOAT => 'Float',
                    TestEnum::BOOL => 'Bool',
                ])
            )->bindToProperty(TestValueObjectWithEnum::NULLABLE_ENUM),
            //
        ]);

    }
}