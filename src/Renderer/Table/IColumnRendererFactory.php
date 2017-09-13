<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Table;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Table\IColumn;

/**
 * The column renderer factory interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IColumnRendererFactory
{
    /**
     * Returns whether this factory supports the supplied column
     *
     * @param IColumn $column
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function accepts(IColumn $column) : bool;

    /**
     * Builds a column renderer for the supplied column.
     *
     * @param IColumn                           $column
     * @param ColumnComponentRendererCollection $componentRenderers
     *
     * @return IColumnRenderer
     * @throws InvalidArgumentException
     */
    public function buildRenderer(IColumn $column, ColumnComponentRendererCollection $componentRenderers) : IColumnRenderer;
}
