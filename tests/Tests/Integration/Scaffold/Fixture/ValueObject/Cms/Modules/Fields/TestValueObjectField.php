<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObject\Cms\Modules\Fields;

use Dms\Core\Common\Crud\Definition\Form\ValueObjectFieldDefinition;
use Dms\Core\Common\Crud\Form\ValueObjectField;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestValueObject;
use Dms\Common\Structure\Field;

/**
 * The Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestValueObject value object field.
 */
class TestValueObjectField extends ValueObjectField
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
        $form->bindTo(TestValueObject::class);

        $form->section('Details', [
            $form->field(
                Field::create('string', 'String')->string()->required()
            )->bindToProperty(TestValueObject::STRING),
            //
            $form->field(
                Field::create('int', 'Int')->int()->required()
            )->bindToProperty(TestValueObject::INT),
            //
            $form->field(
                Field::create('float', 'Float')->decimal()->required()
            )->bindToProperty(TestValueObject::FLOAT),
            //
            $form->field(
                Field::create('bool', 'Bool')->bool()
            )->bindToProperty(TestValueObject::BOOL),
            //
        ]);

    }
}