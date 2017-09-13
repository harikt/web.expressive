<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Table;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Table\IColumnComponent;
use Dms\Web\Expressive\Renderer\Form\FieldRendererCollection;
use Dms\Web\Expressive\Renderer\Form\IFieldRenderer;
use Dms\Web\Expressive\Renderer\Table\Column\Component\FieldComponentRenderer;

/**
 * The column component renderer collection.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ColumnComponentRendererCollection
{
    /**
     * @var IColumnComponentRenderer[]
     */
    protected $columnComponentRenderers;

    /**
     * ColumnComponentRendererCollection constructor.
     *
     * @param IColumnComponentRenderer[]|IFieldRenderer[] $columnComponentRenderers
     */
    public function __construct(array $columnComponentRenderers)
    {
        $fieldsRenderers = [];

        foreach ($columnComponentRenderers as $key => $renderer) {
            if ($renderer instanceof IFieldRenderer) {
                $fieldsRenderers[] = $renderer;
                $columnComponentRenderers[$key] = new FieldComponentRenderer($renderer);
            }
        }

        $collection = new FieldRendererCollection($fieldsRenderers);

        InvalidArgumentException::verifyAllInstanceOf(
            __METHOD__,
            'columnComponentRenderers',
            $columnComponentRenderers,
            IColumnComponentRenderer::class
        );

        $this->columnComponentRenderers = $columnComponentRenderers;
    }

    /**
     * @param IColumnComponent $component
     *
     * @return IColumnComponentRenderer
     * @throws UnrenderableColumnComponentException
     */
    public function findRendererFor(IColumnComponent $component) : IColumnComponentRenderer
    {
        foreach ($this->columnComponentRenderers as $renderer) {
            if ($renderer->accepts($component)) {
                return $renderer;
            }
        }

        throw UnrenderableColumnComponentException::format(
            'Could not render column component \'%s\' with value type %s: no matching renderer could be found',
            $component->getName(),
            get_class($component->getType()->getPhpType()->asTypeString())
        );
    }
}
