<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Table;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Table\IColumn;

/**
 * The column renderer factory collection.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ColumnRendererFactoryCollection
{
    /**
     * @var ColumnComponentRendererCollection
     */
    protected $componentRenderers;

    /**
     * @var IColumnRendererFactory[]
     */
    protected $columnRendererFactories;

    /**
     * ColumnRendererFactoryCollection constructor.
     *
     * @param ColumnComponentRendererCollection $componentRenderers
     * @param IColumnRendererFactory[]          $columnRendererFactories
     */
    public function __construct(ColumnComponentRendererCollection $componentRenderers, array $columnRendererFactories)
    {
        InvalidArgumentException::verifyAllInstanceOf(
            __METHOD__,
            'columnRendererFactories',
            $columnRendererFactories,
            IColumnRendererFactory::class
        );

        $this->componentRenderers = $componentRenderers;
        $this->columnRendererFactories = $columnRendererFactories;
    }

    /**
     * @param IColumn $column
     *
     * @return IColumnRenderer
     * @throws UnrenderableColumnComponentException
     */
    public function buildRendererFor(IColumn $column) : IColumnRenderer
    {
        foreach ($this->columnRendererFactories as $renderer) {
            if ($renderer->accepts($column)) {
                return $renderer->buildRenderer($column, $this->componentRenderers);
            }
        }
        throw UnrenderableColumnComponentException::format(
            'Could not render column \'%s\': no matching renderer could be found',
            $column->getName()
        );
    }
}
