<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Form\Field;

use Dms\Core\Form\Field\Type\FieldType;
use Dms\Core\Form\Field\Type\StringType;
use Dms\Core\Form\IField;
use Dms\Core\Form\IFieldType;
use Dms\Web\Expressive\Renderer\Form\FormRenderingContext;

/**
 * The string field renderer
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class StringFieldRenderer extends BladeFieldRenderer
{
    /**
     * Gets the expected class of the field type for the field.
     *
     * @return array
     */
    public function getFieldTypeClasses() : array
    {
        return [StringType::class];
    }

    protected function canRender(FormRenderingContext $renderingContext, IField $field, IFieldType $fieldType) : bool
    {
        return !$fieldType->has(FieldType::ATTR_OPTIONS)
        && !$fieldType->get(StringType::ATTR_MULTILINE)
        && $fieldType->get(StringType::ATTR_STRING_TYPE) !== StringType::TYPE_HTML;
    }

    protected function renderField(FormRenderingContext $renderingContext, IField $field, IFieldType $fieldType) : string
    {
        /** @var StringType $fieldType */
        $inputType = $this->getInputType($fieldType);

        return $this->renderView(
            $field,
            'dms::components.field.string.input',
            [
                StringType::ATTR_EXACT_LENGTH     => 'exactLength',
                StringType::ATTR_MIN_LENGTH       => 'minLength',
                StringType::ATTR_MAX_LENGTH       => 'maxLength',
                StringType::ATTR_SUGGESTED_VALUES => 'suggestedValues',
            ],
            ['type' => $inputType]
        );
    }

    private function getInputType(StringType $fieldType)
    {
        switch ($fieldType->get(StringType::ATTR_STRING_TYPE)) {
            case StringType::TYPE_URL:
                return 'url';

            case StringType::TYPE_IP_ADDRESS:
                return 'ip-address';

            case StringType::TYPE_EMAIL:
                return 'email';

            case StringType::TYPE_PASSWORD:
                return 'password';

            default:
                return 'text';
        }
    }

    protected function renderFieldValue(FormRenderingContext $renderingContext, IField $field, $value, IFieldType $fieldType) : string
    {
        /** @var StringType $fieldType */
        $inputType = $this->getInputType($fieldType);

        return $this->renderValueViewWithNullDefault(
            $field,
            $value,
            'dms::components.field.string.value',
            [
                'type' => $inputType,
            ]
        );
    }
}
