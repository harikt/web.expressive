<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Chart;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Table\Chart\IChartDataTable;

/**
 * The chart renderer collection.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ChartRendererCollection
{
    /**
     * @var IChartRenderer[]
     */
    protected $chartRenderers;

    /**
     * ChartRendererCollection constructor.
     *
     * @param IChartRenderer[] $chartRenderers
     */
    public function __construct(array $chartRenderers)
    {
        InvalidArgumentException::verifyAllInstanceOf(
            __METHOD__,
            'chartRenderers',
            $chartRenderers,
            IChartRenderer::class
        );

        $this->chartRenderers = $chartRenderers;
    }

    /**
     * @param IChartDataTable $chartData
     *
     * @return IChartRenderer
     * @throws UnrenderableChartException
     */
    public function findRendererFor(IChartDataTable $chartData) : IChartRenderer
    {
        foreach ($this->chartRenderers as $renderer) {
            if ($renderer->accepts($chartData)) {
                return $renderer;
            }
        }

        throw UnrenderableChartException::format(
            'Could not render chart with structure type %s: no matching renderer could be found',
            get_class($chartData->getStructure())
        );
    }
}
