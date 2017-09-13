<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Table\Column;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Table\IColumn;
use Dms\Web\Expressive\Renderer\Table\ColumnComponentRendererCollection;
use Dms\Web\Expressive\Renderer\Table\IColumnComponentRenderer;
use Dms\Web\Expressive\Renderer\Table\IColumnRenderer;
use Dms\Web\Expressive\Renderer\Table\IColumnRendererFactory;

/**
 * The column renderer base class.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ColumnRendererAndFactory implements IColumnRendererFactory
{
    /**
     * Builds a column renderer for the supplied column.
     *
     * @param IColumn                           $column
     * @param ColumnComponentRendererCollection $componentRenderers
     *
     * @return IColumnRenderer
     * @throws InvalidArgumentException
     */
    final public function buildRenderer(IColumn $column, ColumnComponentRendererCollection $componentRenderers) : IColumnRenderer
    {
        if (!$this->accepts($column)) {
            throw InvalidArgumentException::format(
                'Invalid column supplied to %s',
                get_class($this) . '::' . __FUNCTION__
            );
        }

        $resolvedComponentRenderers = [];

        foreach ($column->getComponents() as $component) {
            $resolvedComponentRenderers[$component->getName()] = $componentRenderers->findRendererFor($component);
        }

        return new CallbackColumnRenderer(
            function () use ($column) {
                return $this->renderHeader($column);
            },
            function (array $value) use ($column, $resolvedComponentRenderers) {
                return $this->renderValue($column, $resolvedComponentRenderers, $value);
            }
        );
    }

    /**
     * Renders the column header as a html string.
     *
     * @param IColumn $column
     *
     * @return string
     */
    abstract protected function renderHeader(IColumn $column) : string;

    /**
     * Renders the column value as a html string.
     *
     * @param IColumn                    $column
     * @param IColumnComponentRenderer[] $componentRenderers
     * @param array                      $value
     *
     * @return string
     */
    abstract protected function renderValue(IColumn $column, array $componentRenderers, array $value) : string;
}
