<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Chart;

use Dms\Common\Structure\DateTime\Date;
use Dms\Common\Structure\DateTime\DateOrTimeObject;
use Dms\Common\Structure\DateTime\DateTime;
use Dms\Common\Structure\DateTime\TimeOfDay;
use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Table\Chart\IChartDataTable;
use Dms\Core\Table\Chart\Structure\AreaChart;
use Dms\Core\Table\Chart\Structure\BarChart;
use Dms\Core\Table\Chart\Structure\GraphChart;
use Dms\Core\Table\Chart\Structure\LineChart;

/**
 * The chart renderer for graph charts (eg line, area, bar charts)
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class GraphChartRenderer extends ChartRenderer
{
    /**
     * Returns whether this renderer can render the supplied chart.
     *
     * @param IChartDataTable $chartData
     *
     * @return bool
     */
    public function accepts(IChartDataTable $chartData) : bool
    {
        return $chartData->getStructure() instanceof GraphChart;
    }

    /**
     * @param IChartDataTable $chartData
     *
     * @return string
     */
    protected function renderChart(IChartDataTable $chartData) : string
    {
        /** @var GraphChart $chartStructure */
        $chartStructure = $chartData->getStructure();

        $yAxisKeys   = [];
        $yAxisLabels = [];

        $yCounter = 1;

        foreach ($chartStructure->getVerticalAxis()->getComponents() as $component) {
            $yAxisKeys[]   = 'y' . $yCounter++;
            $yAxisLabels[] = $component->getLabel();
        }

        $chartDataArray = $this->transformChartDataToIndexedArrays(
            $chartData,
            $chartStructure->getHorizontalAxis()->getName(),
            $chartStructure->getHorizontalAxis()->getComponent()->getName(),
            $chartStructure->getVerticalAxis()->getName()
        );

        $dateTimeClass = $chartStructure->getHorizontalAxis()->getType()->getPhpType()->nonNullable()->asTypeString();

        return view('dms::components.chart.graph-chart')
            ->with([
                'chartType'          => $this->getChartType($chartStructure),
                'dateFormat'         => defined($dateTimeClass . '::DISPLAY_FORMAT')
                    ? constant($dateTimeClass . '::DISPLAY_FORMAT')
                    : DateTime::DISPLAY_FORMAT,
                'data'               => $chartDataArray,
                'horizontalAxisKey'  => 'x',
                'verticalAxisKeys'   => $yAxisKeys,
                'verticalAxisLabels' => $yAxisLabels,
                'horizontalUnitType'          => $this->getHorizontalUnitType($dateTimeClass),
            ])
            ->render();
    }

    private function transformChartDataToIndexedArrays(IChartDataTable $data, $xAxisName, $xComponentName, $yAxisName)
    {
        $results = [];

        foreach ($data->getRows() as $row) {
            $resultRow = [];

            $resultRow['x'] = $this->transformValue($row[$xAxisName][$xComponentName]);

            $yCounter = 1;

            foreach ($row[$yAxisName] as $yComponentValue) {
                $resultRow['y' . $yCounter++] = $this->transformValue($yComponentValue);
            }

            $results[] = $resultRow;
        }

        return $results;
    }

    private function getChartType(GraphChart $chartStructure)
    {
        switch (true) {
            case $chartStructure instanceof LineChart:
                return 'line';
            case $chartStructure instanceof AreaChart:
                return 'area';
            case $chartStructure instanceof BarChart:
                return 'bar';

            default:
                throw InvalidArgumentException::format('Unknown chart type %s', get_class($chartStructure));
        }
    }

    private function getHorizontalUnitType(string $dateTimeClass) : string
    {
        switch ($dateTimeClass) {
            case TimeOfDay::class:
                return 'time';
            case Date::class:
                return 'date';

            default:
                return 'datetime';
        }
    }

    private function transformValue($value) : string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format(DateTime::DISPLAY_FORMAT);
        }

        if ($value instanceof DateOrTimeObject) {
            return $value->format($value::DISPLAY_FORMAT);
        }

        return (string)$value;
    }
}
