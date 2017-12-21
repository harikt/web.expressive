<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers\Package\Module\Chart;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\ICms;
use Dms\Core\Model\Criteria\Condition\ConditionOperator;
use Dms\Core\Model\Criteria\OrderingDirection;
use Dms\Core\Module\IChartDisplay;
use Dms\Core\Module\IChartView;
use Dms\Core\Module\IModule;
use Dms\Core\Table\Chart\Criteria\ChartCriteria;
use Dms\Core\Table\Chart\IChartStructure;
use Dms\Web\Expressive\Http\Controllers\DmsController;
use Dms\Web\Expressive\Http\Controllers\Package\Module\ModuleContextTrait;
use Dms\Web\Expressive\Renderer\Chart\ChartControlRenderer;
use Interop\Http\Server\RequestHandlerInterface;
use Interop\Http\Server\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The chart controller
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class LoadChartDataController extends DmsController implements ServerMiddlewareInterface
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

    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate)
    {
        $chartName = $request->getAttribute('chart');
        $viewName = $request->getAttribute('view');

        $moduleContext = $this->getModuleContext($request, $this->router, $this->cms);
        $module = $moduleContext->getModule();

        $chart = $this->loadChart($module, $chartName);

        $chartView = $this->loadChartView($chart, $viewName);

        $criteria = $chartView->getCriteriaCopy() ?: $chart->getDataSource()->criteria();

        $this->filterCriteriaFromRequest($request, $chart->getDataSource()->getStructure(), $criteria);

        $this->loadSharedViewVariables($request);

        return $this->chartRenderer->renderChart(
            $chart->getDataSource()->load($criteria)
        );
    }

    protected function filterCriteriaFromRequest(ServerRequestInterface $request, IChartStructure $structure, ChartCriteria $criteria)
    {
        $axisNames = [];

        foreach ($structure->getAxes() as $axis) {
            $axisNames[] = $axis->getName();
        }

        // $this->validate($request, [
        //     'conditions.*.axis'     => 'required|in:' . implode(',', $axisNames),
        //     'conditions.*.operator' => 'required|in:' . implode(',', ConditionOperator::getAll()),
        //     'conditions.*.value'    => 'required',
        //     'orderings.*.component' => 'required|in:' . implode(',', $axisNames),
        //     'orderings.*.direction' => 'required|in' . implode(',', OrderingDirection::getAll()),
        // ]);

        if ($request->has('conditions')) {
            foreach ($request->input('conditions') as $condition) {
                $criteria->where($condition['axis'], $condition['operator'], $condition['value']);
            }
        }

        if ($request->has('orderings')) {
            foreach ($request->input('orderings') as $ordering) {
                $criteria->orderBy($ordering['component'], $ordering['direction']);
            }
        }
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
