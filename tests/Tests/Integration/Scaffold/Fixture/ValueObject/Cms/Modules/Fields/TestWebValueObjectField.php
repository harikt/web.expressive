<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObject\Cms\Modules\Fields;

use Dms\Core\Common\Crud\Definition\Form\ValueObjectFieldDefinition;
use Dms\Core\Common\Crud\Form\ValueObjectField;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestWebValueObject;
use Dms\Common\Structure\Field;

/**
 * The Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestWebValueObject value object field.
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
                Field::create('email_address', 'Email Address')->email()->required()
            )->bindToProperty(TestWebValueObject::EMAIL_ADDRESS),
            //
            $form->field(
                Field::create('nullable_email_address', 'Nullable Email Address')->email()
            )->bindToProperty(TestWebValueObject::NULLABLE_EMAIL_ADDRESS),
            //
            $form->field(
                Field::create('html', 'Html')->html()->required()
            )->bindToProperty(TestWebValueObject::HTML),
            //
            $form->field(
                Field::create('nullable_html', 'Nullable Html')->html()
            )->bindToProperty(TestWebValueObject::NULLABLE_HTML),
            //
            $form->field(
                Field::create('ip_address', 'Ip Address')->ipAddress()->required()
            )->bindToProperty(TestWebValueObject::IP_ADDRESS),
            //
            $form->field(
                Field::create('nullable_ip_address', 'Nullable Ip Address')->ipAddress()
            )->bindToProperty(TestWebValueObject::NULLABLE_IP_ADDRESS),
            //
            $form->field(
                Field::create('url', 'Url')->url()->required()
            )->bindToProperty(TestWebValueObject::URL),
            //
            $form->field(
                Field::create('nullable_url', 'Nullable Url')->url()
            )->bindToProperty(TestWebValueObject::NULLABLE_URL),
            //
        ]);

    }
}