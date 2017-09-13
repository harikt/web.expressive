<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Form\Field;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Form\Field\Type\FieldType;
use Dms\Core\Form\IField;
use Dms\Core\Form\IFieldType;
use Dms\Web\Expressive\Renderer\Form\FieldRendererCollection;
use Dms\Web\Expressive\Renderer\Form\FormRenderingContext;
use Dms\Web\Expressive\Renderer\Form\IFieldRenderer;

/**
 * The base field renderer
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class FieldRenderer implements IFieldRenderer
{
    /**
     * @var FieldRendererCollection
     */
    protected $fieldRendererCollection;

    /**
     * @var string[]
     */
    protected $fieldTypeClasses;

    /**
     * FieldRenderer constructor.
     */
    public function __construct()
    {
        $this->fieldTypeClasses = $this->getFieldTypeClasses();
    }

    /**
     * @param FieldRendererCollection $fieldRenderer
     *
     * @return void
     */
    public function setRendererCollection(FieldRendererCollection $fieldRenderer)
    {
        $this->fieldRendererCollection = $fieldRenderer;
    }

    /**
     * Returns whether this renderer can render the supplied field.
     *
     * @param FormRenderingContext $renderingContext
     * @param IField               $field
     *
     * @return bool
     */
    final public function accepts(FormRenderingContext $renderingContext, IField $field) : bool
    {
        $type = $field->getType();

        $isCorrectType = false;
        foreach ($this->fieldTypeClasses as $class) {
            if ($type instanceof $class) {
                $isCorrectType = true;
            }
        }

        if (!$isCorrectType) {
            return false;
        }

        return $this->canRender($renderingContext, $field, $type);
    }

    /**
     * @param FormRenderingContext $renderingContext
     * @param IField               $field
     * @param IFieldType           $fieldType
     *
     * @return bool
     */
    abstract protected function canRender(FormRenderingContext $renderingContext, IField $field, IFieldType $fieldType) : bool;

    /**
     * Renders the supplied field input as a html string.
     *
     * @param FormRenderingContext $renderingContext
     * @param IField               $field
     *
     * @return string
     * @throws InvalidArgumentException
     */
    final public function render(FormRenderingContext $renderingContext, IField $field) : string
    {
        if (!$this->accepts($renderingContext, $field)) {
            throw InvalidArgumentException::format(
                'Field \'%s\' cannot be rendered in renderer of type %s',
                $field->getName(),
                get_class($this)
            );
        }

        if ($field->getType()->get(FieldType::ATTR_READ_ONLY)) {
            return $this->renderValue($renderingContext, $field);
        }

        return $this->renderField($renderingContext, $field, $field->getType());
    }

    /**
     * @param FormRenderingContext $renderingContext
     * @param IField               $field
     * @param IFieldType           $fieldType
     *
     * @return string
     */
    abstract protected function renderField(FormRenderingContext $renderingContext, IField $field, IFieldType $fieldType) : string;

    /**
     * Renders the supplied field value as a html string.
     *
     * @param FormRenderingContext $renderingContext
     * @param IField               $field
     * @param mixed                $overrideValue
     *
     * @return string
     * @throws InvalidArgumentException
     */
    final public function renderValue(FormRenderingContext $renderingContext, IField $field, $overrideValue = null) : string
    {
        if (!$this->accepts($renderingContext, $field)) {
            throw InvalidArgumentException::format(
                'Field \'%s\' cannot be rendered in renderer of type %s',
                $field->getName(),
                get_class($this)
            );
        }

        return $this->renderFieldValue($renderingContext, $field, $overrideValue ?? $field->getUnprocessedInitialValue(), $field->getType());
    }

    /**
     * @param FormRenderingContext $renderingContext
     * @param IField               $field
     * @param mixed                $value
     * @param IFieldType           $fieldType
     *
     * @return string
     */
    abstract protected function renderFieldValue(FormRenderingContext $renderingContext, IField $field, $value, IFieldType $fieldType) : string;
}
