<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObject\Cms\Modules\Fields;

use Dms\Core\Common\Crud\Definition\Form\ValueObjectFieldDefinition;
use Dms\Core\Common\Crud\Form\ValueObjectField;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestFileValueObject;
use Dms\Common\Structure\Field;

/**
 * The Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestFileValueObject value object field.
 */
class TestFileValueObjectField extends ValueObjectField
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
        $form->bindTo(TestFileValueObject::class);

        $form->section('Details', [
            $form->field(
                Field::create('file', 'File')
                    ->file()
                    ->required()
                    ->moveToPathWithRandomFileName(public_path('app/test_file_value_object'))
            )->bindToProperty(TestFileValueObject::FILE),
            //
            $form->field(
                Field::create('nullable_file', 'Nullable File')
                    ->file()
                    ->moveToPathWithRandomFileName(public_path('app/test_file_value_object'))
            )->bindToProperty(TestFileValueObject::NULLABLE_FILE),
            //
            $form->field(
                Field::create('image', 'Image')
                    ->image()
                    ->required()
                    ->moveToPathWithRandomFileName(public_path('app/test_file_value_object'))
            )->bindToProperty(TestFileValueObject::IMAGE),
            //
            $form->field(
                Field::create('nullable_image', 'Nullable Image')
                    ->image()
                    ->moveToPathWithRandomFileName(public_path('app/test_file_value_object'))
            )->bindToProperty(TestFileValueObject::NULLABLE_IMAGE),
            //
        ]);

    }
}