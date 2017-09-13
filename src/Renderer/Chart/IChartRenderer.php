<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Chart;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Table\Chart\IChartDataTable;

/**
 * The chart renderer interface
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IChartRenderer
{
    /**
     * Returns whether this renderer can render the supplied chart.
     *
     * @param IChartDataTable $chartData
     *
     * @return bool
     */
    public function accepts(IChartDataTable $chartData) : bool;

    /**
     * Renders the supplied chart input as a html string.
     *
     * @param IChartDataTable $chartDataData
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public function render(IChartDataTable $chartDataData) : string;
}
