<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Form\Field;

use Dms\Common\Structure\DateTime\Form\DateTimeType;
use Dms\Common\Structure\DateTime\Form\DateType;
use Dms\Common\Structure\DateTime\Form\TimeOfDayType;
use Dms\Common\Structure\DateTime\Form\TimezonedDateTimeType;
use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Form\Field\Type\DateTimeTypeBase;
use Dms\Core\Form\Field\Type\FieldType;
use Dms\Core\Form\IField;
use Dms\Core\Form\IFieldType;
use Dms\Web\Expressive\Renderer\Form\FormRenderingContext;

/**
 * The date field renderer
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DateOrTimeFieldRenderer extends BladeFieldRenderer
{
    /**
     * Gets the expected class of the field type for the field.
     *
     * @return array
     */
    public function getFieldTypeClasses() : array
    {
        return [DateTimeTypeBase::class];
    }

    protected function canRender(FormRenderingContext $renderingContext, IField $field, IFieldType $fieldType) : bool
    {
        return !$fieldType->has(FieldType::ATTR_OPTIONS);
    }

    protected function renderField(FormRenderingContext $renderingContext, IField $field, IFieldType $fieldType) : string
    {
        $mode = $this->getMode($fieldType);

        return $this->renderView(
            $field,
            'dms::components.field.date-or-time.single.input',
            [
                DateTimeTypeBase::ATTR_FORMAT => 'format',
                DateTimeTypeBase::ATTR_MIN    => 'min',
                DateTimeTypeBase::ATTR_MAX    => 'max',
                // TODO: less_than and greater_than
            ],
            [
                'mode' => $mode,
            ]
        );
    }

    protected function renderFieldValue(FormRenderingContext $renderingContext, IField $field, $value, IFieldType $fieldType) : string
    {
        $mode = $this->getMode($fieldType);

        return $this->renderValueViewWithNullDefault(
            $field,
            $value,
            'dms::components.field.date-or-time.single.value',
            [
                'format' => $fieldType->get(DateTimeTypeBase::ATTR_FORMAT),
                'mode'   => $mode,
            ]
        );
    }

    private function getMode(IFieldType $fieldType) : string
    {
        if ($fieldType instanceof DateType) {
            return 'date';
        }

        if ($fieldType instanceof TimeOfDayType) {
            return 'time';
        }

        if ($fieldType instanceof DateTimeType) {
            return 'date-time';
        }

        if ($fieldType instanceof TimezonedDateTimeType) {
            return 'timezoned-date-time';
        }

        throw InvalidArgumentException::format('Unknown date field type: %s', get_class($fieldType));
    }
}
