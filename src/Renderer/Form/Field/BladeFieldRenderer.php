<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Form\Field;

use Dms\Core\Form\Field\Type\FieldType;
use Dms\Core\Form\IField;

/**
 * The blade field renderer
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class BladeFieldRenderer extends FieldRenderer
{
    /**
     * @var array
     */
    protected $defaultAttributeMap = [
        FieldType::ATTR_READ_ONLY => 'readonly',
        FieldType::ATTR_REQUIRED  => 'required',
        FieldType::ATTR_DEFAULT   => 'defaultValue',
    ];

    /**
     * @param IField $field
     * @param string $viewName
     * @param array  $attributeVariableMap
     * @param array  $extraParams
     *
     * @return string
     */
    protected function renderView(IField $field, string $viewName, array $attributeVariableMap = [], array $extraParams = []) : string
    {
        $attributeVariableMap += $this->defaultAttributeMap;
        $fieldType = $field->getType();

        $viewParams = [];

        foreach ($attributeVariableMap as $attribute => $variableName) {
            $viewParams[$variableName] = $fieldType->get($attribute);
        }

        $viewParams['value'] = $field->getUnprocessedInitialValue();
        $viewParams['processedValue'] = $field->getInitialValue();

        return view($viewName)
            ->with('field', $field)
            ->with('name', $field->getName())
            ->with('label', $field->getLabel())
            ->with('placeholder', $fieldType->get('placeholder') ?: $field->getLabel())
            ->with('fieldType', $fieldType)
            ->with($viewParams)
            ->with($extraParams)
            ->render();
    }


    /**
     * @param IField $field
     * @param mixed  $value
     * @param string $viewName
     * @param array  $extraParams
     *
     * @return string
     */
    protected function renderValueViewWithNullDefault(
        IField $field,
        $value,
        string $viewName,
        array $extraParams = []
    ) : string {
        if ($value === null) {
            return view('dms::components.field.null.value')
                ->render();
        }

        return view($viewName)
            ->with('name', $field->getName())
            ->with('label', $field->getLabel())
            ->with('value', $value)
            ->with($extraParams)
            ->render();
    }
}
