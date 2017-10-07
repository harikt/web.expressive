<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Cms\Modules\Fields;

use Dms\Core\Common\Crud\Definition\Form\ValueObjectFieldDefinition;
use Dms\Core\Common\Crud\Form\ValueObjectField;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestWebValueObject;
use Dms\Common\Structure\Field;

/**
 * The Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestWebValueObject value object field.
 */
class TestWebValueObjectField extends ValueObjectField
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
        $form->bindTo(TestWebValueObject::class);

        $form->section('Details', [
            $form->field(
                Field::create('email_address', 'Email Address')->arrayOf(
                    Field::element()->email()->required()
                )
            )->bindToProperty(TestWebValueObject::EMAIL_ADDRESS),
            //
            $form->field(
                Field::create('html', 'Html')->arrayOf(
                    Field::element()->html()->required()
                )
            )->bindToProperty(TestWebValueObject::HTML),
            //
            $form->field(
                Field::create('ip_address', 'Ip Address')->arrayOf(
                    Field::element()->ipAddress()->required()
                )
            )->bindToProperty(TestWebValueObject::IP_ADDRESS),
            //
            $form->field(
                Field::create('url', 'Url')->arrayOf(
                    Field::element()->url()->required()
                )
            )->bindToProperty(TestWebValueObject::URL),
            //
        ]);
    }
}
