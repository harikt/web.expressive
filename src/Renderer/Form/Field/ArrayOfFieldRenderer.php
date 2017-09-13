<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Form\Field;

use Dms\Core\Form\Field\Builder\Field;
use Dms\Core\Form\Field\Type\ArrayOfType;
use Dms\Core\Form\Field\Type\FieldType;
use Dms\Core\Form\IField;
use Dms\Core\Form\IFieldType;
use Dms\Web\Expressive\Renderer\Form\FormRenderingContext;

/**
 * The array of field renderer
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ArrayOfFieldRenderer extends BladeFieldRenderer
{
    /**
     * Gets the expected class of the field type for the field.
     *
     * @return array
     */
    public function getFieldTypeClasses() : array
    {
        return [ArrayOfType::class];
    }

    protected function canRender(FormRenderingContext $renderingContext, IField $field, IFieldType $fieldType) : bool
    {
        /** @var ArrayOfType $fieldType */
        return !$fieldType->has(FieldType::ATTR_OPTIONS);
    }

    protected function renderField(
        FormRenderingContext $renderingContext,
        IField $field,
        IFieldType $fieldType
    ) : string {
        /** @var ArrayOfType $fieldType */
        $elementField = $this->makeElementField($fieldType);

        return $this->renderView(
            $field,
            'dms::components.field.list-of-fields.input',
            [
                ArrayOfType::ATTR_MIN_ELEMENTS   => 'minElements',
                ArrayOfType::ATTR_MAX_ELEMENTS   => 'maxElements',
                ArrayOfType::ATTR_EXACT_ELEMENTS => 'exactElements',
            ],
            [
                'renderingContext' => $renderingContext,
                'elementField'     => $elementField,
                'fieldRenderer'    => $this->fieldRendererCollection->findRendererFor($renderingContext, $elementField),
            ]
        );
    }

    public function renderFieldValue(
        FormRenderingContext $renderingContext,
        IField $field,
        $value,
        IFieldType $fieldType
    ) : string {
        /** @var ArrayOfType $fieldType */
        $elementField = $this->makeElementField($fieldType);

        return $this->renderValueViewWithNullDefault(
            $field,
            $value,
            'dms::components.field.list-of-fields.value',
            [
                'renderingContext' => $renderingContext,
                'elementField'     => $elementField,
                'fieldRenderer'    => $this->fieldRendererCollection->findRendererFor($renderingContext, $elementField),
                'processedValue'   => $value === null ? null : $field->process($value),
            ]
        );
    }

    protected function makeElementField(ArrayOfType $fieldType) : IField
    {
        return $fieldType->getElementField();
    }
}
