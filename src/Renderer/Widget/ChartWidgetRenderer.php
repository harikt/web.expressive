<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Widget;

use Dms\Core\Module\IModule;
use Dms\Core\Widget\ChartWidget;
use Dms\Core\Widget\IWidget;
use Dms\Web\Expressive\Http\ModuleContext;
use Dms\Web\Expressive\Renderer\Chart\ChartRendererCollection;

/**
 * The widget renderer for charts
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ChartWidgetRenderer extends WidgetRenderer
{
    /**
     * @var ChartRendererCollection
     */
    protected $chartRenderers;

    /**
     * ChartWidgetRenderer constructor.
     *
     * @param ChartRendererCollection $chartRenderers
     */
    public function __construct(ChartRendererCollection $chartRenderers)
    {
        $this->chartRenderers = $chartRenderers;
    }

    public function accepts(ModuleContext $moduleContext, IWidget $widget) : bool
    {
        return $widget instanceof ChartWidget;
    }

    /**
     * Gets an array of links for the supplied widget.
     *
     * @param ModuleContext $moduleContext
     * @param IWidget $widget
     *
     * @return array
     */
    protected function getWidgetLinks(ModuleContext $moduleContext, IWidget $widget) : array
    {
        /** @var ChartWidget $widget */
        $chartDisplay = $widget->getChartDisplay();

        $links = [];

        foreach ($chartDisplay->getViews() as $chartView) {
            $viewParams = [$chartDisplay->getName(), $chartView->getName()];

            $links[$moduleContext->getUrl('chart.view.show', $viewParams)] = $chartView->getLabel();
        }

        if (!$links) {
            $links[$moduleContext->getUrl('chart.view.show', [$chartDisplay->getName(), 'all'])] = 'All';
        }

        return $links;
    }

    /**
     * Renders the supplied widget input as a html string.
     *
     * @param ModuleContext $moduleContext
     * @param IWidget $widget
     *
     * @return string
     */
    protected function renderWidget(ModuleContext $moduleContext, IWidget $widget) : string
    {
        /** @var ChartWidget $widget */
        $chartData = $widget->loadData();

        return view('dms::components.widget.chart')
            ->with([
                'chartContent' => $this->chartRenderers->findRendererFor($chartData)->render($chartData),
            ])
            ->render();
    }
}
