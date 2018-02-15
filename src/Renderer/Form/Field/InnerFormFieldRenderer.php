<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Form\Field;

use Dms\Common\Structure\DateTime\Form\DateOrTimeRangeType;
use Dms\Common\Structure\FileSystem\Form\FileUploadType;
use Dms\Common\Structure\Money\Form\MoneyType;
use Dms\Core\Form\Field\Type\FieldType;
use Dms\Core\Form\Field\Type\InnerFormType;
use Dms\Core\Form\IField;
use Dms\Core\Form\IFieldType;
use Dms\Web\Expressive\Renderer\Form\DefaultFormRenderer;
use Dms\Web\Expressive\Renderer\Form\FormRenderingContext;

/**
 * The inner-form field renderer
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class InnerFormFieldRenderer extends BladeFieldRenderer
{
    /**
     * Gets the expected class of the field type for the field.
     *
     * @return array
     */
    public function getFieldTypeClasses() : array
    {
        return [InnerFormType::class];
    }

    protected function canRender(FormRenderingContext $renderingContext, IField $field, IFieldType $fieldType) : bool
    {
        return !$fieldType->has(FieldType::ATTR_OPTIONS)
        && !($fieldType instanceof DateOrTimeRangeType)
        && !($fieldType instanceof FileUploadType)
        && !($fieldType instanceof MoneyType);
    }

    protected function renderField(
        FormRenderingContext $renderingContext,
        IField $field,
        IFieldType $fieldType
    ) : string {
        /**
 * @var InnerFormType $fieldType
*/
        $formWithArrayFields = $fieldType->getInnerArrayForm($field->getName());
        $formRenderer        = new DefaultFormRenderer($this->fieldRendererCollection, $this->template);


        return $this->renderView(
            $field,
            'dms::components.field.inner-form.input',
            [],
            [
                'formContent' => $formRenderer->renderFields($renderingContext, $formWithArrayFields),
            ]
        );
    }

    protected function renderFieldValue(
        FormRenderingContext $renderingContext,
        IField $field,
        $value,
        IFieldType $fieldType
    ) : string {
        /**
 * @var InnerFormType $fieldType
*/
        $fieldType = $field->withInitialValue($field->process($value))->getType();

        $formWithArrayFields = $fieldType->getInnerArrayForm($field->getName());
        $formRenderer        = new DefaultFormRenderer($this->fieldRendererCollection, $this->template);

        return $this->renderValueViewWithNullDefault(
            $field,
            $value,
            'dms::components.field.inner-form.value',
            [
                'formContent' => $formRenderer->renderFieldsAsValues($renderingContext, $formWithArrayFields),
            ]
        );
    }
}
