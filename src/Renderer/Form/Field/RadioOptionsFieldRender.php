<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Form\Field;

use Dms\Core\Form\Field\Type\FieldType;
use Dms\Core\Form\IField;
use Dms\Core\Form\IFieldType;
use Dms\Web\Expressive\Renderer\Form\FormRenderingContext;

/**
 * The radio-group options field renderer
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class RadioOptionsFieldRender extends OptionsFieldRender
{
    /**
     * Gets the expected class of the field type for the field.
     *
     * @return array
     */
    public function getFieldTypeClasses() : array
    {
        return [FieldType::class];
    }

    protected function canRender(FormRenderingContext $renderingContext, IField $field, IFieldType $fieldType) : bool
    {
        return $fieldType->has(FieldType::ATTR_OPTIONS)
        && $fieldType->get(FieldType::ATTR_SHOW_ALL_OPTIONS);
    }

    protected function renderField(
        FormRenderingContext $renderingContext,
        IField $field,
        IFieldType $fieldType
    ) : string {
        return $this->renderView(
            $field,
            'dms::components.field.radio-group.input',
            [
                FieldType::ATTR_OPTIONS => 'options',
            ]
        );
    }
}
