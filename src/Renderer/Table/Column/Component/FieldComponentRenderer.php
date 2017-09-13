<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Table\Column\Component;

use Dms\Core\Model\Criteria\Condition\ConditionOperator;
use Dms\Core\Table\IColumnComponent;
use Dms\Web\Expressive\Http\CurrentModuleContext;
use Dms\Web\Expressive\Renderer\Form\FormRenderingContext;
use Dms\Web\Expressive\Renderer\Form\IFieldRenderer;
use Dms\Web\Expressive\Renderer\Table\IColumnComponentRenderer;

/**
 * The field component renderer.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FieldComponentRenderer implements IColumnComponentRenderer
{
    /**
     * @var IFieldRenderer
     */
    protected $fieldRenderer;

    /**
     * @var FormRenderingContext
     */
    protected $renderingContext;

    /**
     * FieldComponentRenderer constructor.
     *
     * @param IFieldRenderer $fieldRenderer
     */
    public function __construct(IFieldRenderer $fieldRenderer)
    {
        $this->fieldRenderer = $fieldRenderer;
    }

    protected function getRenderingContext() : FormRenderingContext
    {
        if (!$this->renderingContext) {
            $this->renderingContext = new FormRenderingContext(CurrentModuleContext::getInstance());
        }

        return $this->renderingContext;
    }

    /**
     * @param IColumnComponent $component
     *
     * @return bool
     */
    public function accepts(IColumnComponent $component) : bool
    {
        return $this->fieldRenderer->accepts(
            $this->getRenderingContext(),
            $component->getType()->getOperator(ConditionOperator::EQUALS)->getField()
        );
    }

    /**
     * Renders the supplied column component value as a html string.
     *
     * @param IColumnComponent $component
     * @param mixed            $value
     *
     * @return string
     */
    public function render(IColumnComponent $component, $value) : string
    {
        $field = $component->getType()->getOperator(ConditionOperator::EQUALS)->getField();

        return $this->fieldRenderer->renderValue($this->getRenderingContext(), $field, $field->unprocess($value));
    }
}
