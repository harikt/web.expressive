<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Cms\Modules\Fields;

use Dms\Core\Common\Crud\Definition\Form\ValueObjectFieldDefinition;
use Dms\Core\Common\Crud\Form\ValueObjectField;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestColourValueObject;
use Dms\Common\Structure\Field;

/**
 * The Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestColourValueObject value object field.
 */
class TestColourValueObjectField extends ValueObjectField
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
        $form->bindTo(TestColourValueObject::class);

        $form->section('Details', [
            $form->field(
                Field::create('colour', 'Colour')->arrayOf(
                    Field::element()->colour()->required()
                )
            )->bindToProperty(TestColourValueObject::COLOUR),
            //
            $form->field(
                Field::create('transparent_colour', 'Transparent Colour')->arrayOf(
                    Field::element()->colourWithTransparency()->required()
                )
            )->bindToProperty(TestColourValueObject::TRANSPARENT_COLOUR),
            //
        ]);

    }
}