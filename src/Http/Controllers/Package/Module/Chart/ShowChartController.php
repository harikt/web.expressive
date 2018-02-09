<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers\Package\Module\Chart;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\ICms;
use Dms\Core\Module\IChartDisplay;
use Dms\Core\Module\IChartView;
use Dms\Core\Module\IModule;
use Dms\Web\Expressive\Http\Controllers\DmsController;
use Dms\Web\Expressive\Http\Controllers\Package\Module\ModuleContextTrait;
use Dms\Web\Expressive\Renderer\Chart\ChartControlRenderer;
use Dms\Web\Expressive\Util\StringHumanizer;
use Psr\Http\Server\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The chart controller
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ShowChartController extends DmsController implements ServerMiddlewareInterface
{
    use ModuleContextTrait;

    /**
     * @var ChartControlRenderer
     */
    protected $chartRenderer;

    public function __construct(
        ICms $cms,
        IAuthSystem $auth,
        TemplateRendererInterface $template,
        RouterInterface $router,
        ChartControlRenderer $chartRenderer
    ) {
        parent::__construct($cms, $auth, $template, $router);
        $this->chartRenderer = $chartRenderer;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $chartName = $request->getAttribute('chart');
        $viewName = $request->getAttribute('view');

        $moduleContext = $this->getModuleContext($request, $this->router, $this->cms);
        $module = $moduleContext->getModule();
        $chart  = $this->loadChart($module, $chartName);

        $this->loadChartView($chart, $viewName);

        return new HtmlResponse(
            $this->template->render(
                'dms::package.module.chart',
                [
                    'assetGroups'     => ['charts'],
                    'pageTitle'       => implode(' :: ', array_merge($moduleContext->getTitles(), [StringHumanizer::title($chartName)])),
                    'pageSubTitle'    => $viewName,
                    'breadcrumbs'     => $moduleContext->getBreadcrumbs(),
                    'finalBreadcrumb' => StringHumanizer::title($chartName),
                    'chartContent'    => $this->chartRenderer->renderChartControl($moduleContext, $chart, $viewName),
                ]
            )
        );
    }

    /**
     * @param IChartDisplay $chart
     * @param string        $chartView
     *
     * @return IChartView
     */
    protected function loadChartView(IChartDisplay $chart, string $chartView) : IChartView
    {
        try {
            return $chart->hasView($chartView) ? $chart->getView($chartView) : $chart->getDefaultView();
        } catch (InvalidArgumentException $e) {
            return $this->abort($request, 404);
        }
    }

    /**
     * @param IModule $module
     * @param string  $chartName
     *
     * @return IChartDisplay
     */
    protected function loadChart(IModule $module, string $chartName) : IChartDisplay
    {
        try {
            $action = $module->getChart($chartName);

            return $action;
        } catch (InvalidArgumentException $e) {
            $response = new JsonResponse([
                'message' => 'Invalid chart name',
            ], 404);
        }

        return $response;
    }
}
