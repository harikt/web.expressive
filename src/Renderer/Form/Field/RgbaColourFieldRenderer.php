<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Form\Field;

use Dms\Common\Structure\Colour\Form\TransparentColourType;
use Dms\Core\Form\Field\Type\FieldType;
use Dms\Core\Form\IField;
use Dms\Core\Form\IFieldType;
use Dms\Web\Expressive\Renderer\Form\FormRenderingContext;

/**
 * The rgba colour field renderer
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class RgbaColourFieldRenderer extends BladeFieldRenderer
{
    /**
     * Gets the expected class of the field type for the field.
     *
     * @return array
     */
    public function getFieldTypeClasses() : array
    {
        return [TransparentColourType::class];
    }

    protected function canRender(FormRenderingContext $renderingContext, IField $field, IFieldType $fieldType) : bool
    {
        return !$fieldType->has(FieldType::ATTR_OPTIONS);
    }

    protected function renderField(FormRenderingContext $renderingContext, IField $field, IFieldType $fieldType) : string
    {
        return $this->renderView(
            $field,
            'dms::components.field.colour.rgba.input'
        );
    }

    protected function renderFieldValue(FormRenderingContext $renderingContext, IField $field, $value, IFieldType $fieldType) : string
    {
        return $this->renderValueViewWithNullDefault(
            $field,
            $value,
            'dms::components.field.colour.rgba.value'
        );
    }
}
