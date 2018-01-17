<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Form\Field;

use Dms\Core\Form\Field\Type\FieldType;
use Dms\Core\Form\IField;
use Dms\Core\Form\IFieldOptions;
use Dms\Core\Form\IFieldType;
use Dms\Web\Expressive\Renderer\Form\FormRenderingContext;

/**
 * The options field renderer base class
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class OptionsFieldRender extends BladeFieldRenderer
{
    protected function renderFieldValue(
        FormRenderingContext $renderingContext,
        IField $field,
        $value,
        IFieldType $fieldType
    ) : string {
        /** @var IFieldOptions $options */
        $options     = $fieldType->get(FieldType::ATTR_OPTIONS);
        $urlCallback = $this->relatedEntityLinker->getUrlCallbackFor($options);

        $label = null;
        $url   = null;

        try {
            $option = $options->getOptionForValue($value);
            $label  = $option->getLabel();
            $url    = $urlCallback ? $urlCallback($option->getValue()) : null;
        } catch (\Exception $e) {
        }

        return $this->renderValueViewWithNullDefault(
            $field,
            $label,
            'dms::components.field.string.value',
            ['url' => $url]
        );
    }
}
