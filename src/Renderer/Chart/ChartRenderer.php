<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Chart;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Table\Chart\IChartDataTable;

/**
 * The chart renderer base class.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ChartRenderer implements IChartRenderer
{
    /**
     * Renders the supplied chart input as a html string.
     *
     * @param IChartDataTable $chartData
     *
     * @return string
     * @throws InvalidArgumentException
     */
    final public function render(IChartDataTable $chartData) : string
    {
        if (!$this->accepts($chartData)) {
            throw InvalidArgumentException::format(
                'Invalid chart supplied to %s',
                get_class($this) . '::' . __FUNCTION__
            );
        }

        return $this->renderChart($chartData);
    }

    /**
     * Renders the supplied chart input as a html string.
     *
     * @param IChartDataTable $chartData
     *
     * @return string
     */
    abstract protected function renderChart(IChartDataTable $chartData) : string;
}
