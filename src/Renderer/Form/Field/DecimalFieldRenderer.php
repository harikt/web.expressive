<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Form\Field;

use Dms\Core\Form\Field\Type\FieldType;
use Dms\Core\Form\Field\Type\FloatType;
use Dms\Core\Form\IField;
use Dms\Core\Form\IFieldType;
use Dms\Web\Expressive\Renderer\Form\FormRenderingContext;

/**
 * The decimal field renderer
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DecimalFieldRenderer extends BladeFieldRenderer
{
    /**
     * Gets the expected class of the field type for the field.
     *
     * @return array
     */
    public function getFieldTypeClasses() : array
    {
        return [FloatType::class];
    }

    protected function canRender(FormRenderingContext $renderingContext, IField $field, IFieldType $fieldType) : bool
    {
        return !$fieldType->has(FieldType::ATTR_OPTIONS);
    }

    protected function renderField(FormRenderingContext $renderingContext, IField $field, IFieldType $fieldType) : string
    {
        return $this->renderView(
            $field,
            'dms::components.field.number.input',
            [
                FloatType::ATTR_MIN                => 'min',
                FloatType::ATTR_MAX                => 'max',
                FloatType::ATTR_MIN                => 'min',
                FloatType::ATTR_GREATER_THAN       => 'greaterThan',
                FloatType::ATTR_LESS_THAN          => 'lessThan',
                FloatType::ATTR_MAX_DECIMAL_POINTS => 'maxDecimalPlaces',
            ],
            [
                'decimalNumber' => true
            ]
        );
    }

    protected function renderFieldValue(FormRenderingContext $renderingContext, IField $field, $value, IFieldType $fieldType) : string
    {
        return $this->renderValueViewWithNullDefault(
            $field,
            $value,
            'dms::components.field.number.value'
        );
    }
}
