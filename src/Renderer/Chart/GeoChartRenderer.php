<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Chart;

use Dms\Common\Structure\Geo\Chart\GeoChart;
use Dms\Common\Structure\Geo\Chart\GeoCityChart;
use Dms\Common\Structure\Geo\LatLng;
use Dms\Core\Model\Object\Enum;
use Dms\Core\Table\Chart\IChartDataTable;

/**
 * The chart renderer for geo charts
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class GeoChartRenderer extends ChartRenderer
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
        return $chartData->getStructure() instanceof GeoChart;
    }

    /**
     * @param IChartDataTable $chartData
     *
     * @return string
     */
    protected function renderChart(IChartDataTable $chartData) : string
    {
        /** @var GeoChart $chartStructure */
        $chartStructure = $chartData->getStructure();


        $region              = null;
        $latLngAxisName      = null;
        $latLngComponentName = null;

        if ($chartStructure instanceof GeoCityChart) {
            /** @var GeoCityChart $chartStructure */
            $region = $chartStructure->getMapCountry() ? $chartStructure->getMapCountry()->getValue() : null;

            if ($chartStructure->hasCityLatLngAxis()) {
                $latLngAxisName      = $chartStructure->getCityLatLngAxis()->getName();
                $latLngComponentName = $chartStructure->getCityLatLngAxis()->getComponent()->getName();
            }
        }

        $chartDataArray = $this->transformChartDataToIndexedArrays(
            $chartData,
            $chartStructure->getLocationAxis()->getName(),
            $chartStructure->getLocationAxis()->getComponent()->getName(),
            $chartStructure->getValueAxis()->getName(),
            $latLngAxisName,
            $latLngComponentName
        );

        $valueLabels = [];

        foreach ($chartStructure->getValueAxis()->getComponents() as $component) {
            $valueLabels[] = $component->getLabel();
        }

        return $this->template->render(
            'dms::components.chart.geo-chart',
            [
                'cityChart'     => $chartStructure instanceof GeoCityChart,
                'data'          => $chartDataArray,
                'locationLabel' => $chartStructure->getLocationAxis()->getLabel(),
                'valueLabels'   => $valueLabels,
                'region'        => $region,
                'hasLatLng'     => $latLngAxisName !== null,
            ]
        );
    }


    protected function transformChartDataToIndexedArrays(
        IChartDataTable $data,
        string $labelAxisName,
        string $labelComponentName,
        string $valueAxisName,
        string $latLngAxisName = null,
        string $latLngComponentName = null
    ) {
        $results = [];

        foreach ($data->getRows() as $row) {
            $key = $row[$labelAxisName][$labelComponentName];

            if ($key instanceof Enum) {
                $key = $key->getValue();
            }

            if (isset($results[$key])) {
                foreach ($row[$valueAxisName] as $componentName => $value) {
                    $results[$key]['values'][$componentName] += $value;
                }
            } else {
                $results[$key] = [
                    'label'  => $key,
                    'values' => $row[$valueAxisName],
                ];

                if ($latLngAxisName) {
                    /** @var LatLng $latLng */
                    $latLng                   = $row[$latLngAxisName][$latLngComponentName];
                    $results[$key]['lat_lng'] = [$latLng->getLat(), $latLng->getLng()];
                }
            }
        }

        foreach ($results as &$result) {
            $result['values'] = array_values($result['values']);
        }
        unset($result);

        return array_values($results);
    }
}
