<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers\Package\Module\Table;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Common\Crud\Table\ISummaryTable;
use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\ICms;
use Dms\Core\Model\Criteria\Condition\ConditionOperator;
use Dms\Core\Model\Criteria\OrderingDirection;
use Dms\Core\Module\IModule;
use Dms\Core\Module\ITableDisplay;
use Dms\Core\Module\ITableView;
use Dms\Core\Table\Criteria\RowCriteria;
use Dms\Core\Table\ITableStructure;
use Dms\Web\Expressive\Error\DmsError;
use Dms\Web\Expressive\Http\Controllers\DmsController;
use Dms\Web\Expressive\Http\ModuleContext;
use Dms\Web\Expressive\Renderer\Table\TableRenderer;
use Dms\Web\Expressive\Util\StringHumanizer;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

use Dms\Web\Expressive\Http\CurrentModuleContext;

/**
 * The table controller
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class LoadTableRowsController extends DmsController implements ServerMiddlewareInterface
{
    /**
     * @var TableRenderer
     */
    protected $tableRenderer;

    protected $template;

    protected $router;

    /**
     * TableController constructor.
     *
     * @param ICms          $cms
     * @param TableRenderer $tableRenderer
     */
    public function __construct(
        ICms $cms,
        IAuthSystem $auth,
        TableRenderer $tableRenderer,
        TemplateRendererInterface $template,
        RouterInterface $router
    ) {
        parent::__construct($cms, $auth);
        $this->tableRenderer = $tableRenderer;
        $this->template = $template;
        $this->router = $router;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $packageName = $request->getAttribute('package');
        $moduleName = $request->getAttribute('module');
        $tableName = $request->getAttribute('table');
        $viewName = $request->getAttribute('view');
        $package = $this->cms->loadPackage($packageName);
        $moduleContext = ModuleContext::rootContext($this->router, $packageName, $moduleName, function () use ($package, $moduleName) {
            return $package->loadModule($moduleName);
        });

        CurrentModuleContext::setInstance($moduleContext);

        $table = $this->loadTable($moduleContext->getModule(), $tableName);

        $tableView = $this->loadTableView($table, $viewName);

        $criteria = $tableView->getCriteriaCopy() ?: $table->getDataSource()->criteria()->loadAll();

        $isFiltered = $this->filterCriteriaFromRequest($request, $table->getDataSource()->getStructure(), $criteria);

        $this->loadSharedViewVariables($request);

        return new HtmlResponse($this->tableRenderer->renderTableData(
            $moduleContext,
            $table,
            $table->getDataSource()->load($criteria),
            $viewName,
            $isFiltered
        ));
    }

    protected function filterCriteriaFromRequest(ServerRequestInterface $request, ITableStructure $structure, RowCriteria $criteria) : bool
    {
        $validComponentIds = [];

        foreach ($structure->getColumns() as $column) {
            foreach ($column->getComponents() as $component) {
                $validComponentIds[] = $column->getName() . '.' . $component->getName();
            }
        }

        // $this->validate($request, [
        //     'offset'                 => 'integer|min:0',
        //     'amount'                 => 'integer|min:0',
        //     'condition_mode'         => 'required|in:or,and',
        //     'conditions.*.component' => 'required|in:' . implode(',', $validComponentIds),
        //     'conditions.*.operator'  => 'required|in:' . implode(',', ConditionOperator::getAll()),
        //     'conditions.*.value'     => 'required',
        //     'orderings.*.component'  => 'required|in:' . implode(',', $validComponentIds),
        //     'orderings.*.direction'  => 'required|in:' . implode(',', OrderingDirection::getAll()),
        // ]);

        if (isset($request->getParsedBody()['offset'])) {
            $criteria->skipRows((int)$request->getParsedBody()['offset'] + $criteria->getRowsToSkip());
        }

        if (isset($request->getParsedBody()['max_rows'])) {
            $criteria->maxRows(min((int)$request->getParsedBody()['max_rows'], $criteria->getAmountOfRows() ?: PHP_INT_MAX));
        }

        $isFiltered = false;

        if (isset($request->getParsedBody()['conditions'])) {
            $isFiltered = true;

            $criteria->setConditionMode($request->getParsedBody()['condition_mode']);

            foreach ($request->getParsedBody()['conditions'] as $condition) {
                $criteria->where($condition['component'], $condition['operator'], $condition['value']);
            }
        }

        if (isset($request->getParsedBody()['orderings'])) {
            $isFiltered = true;

            $criteria->clearOrderings();
            foreach ($request->getParsedBody()['orderings'] as $ordering) {
                $criteria->orderBy($ordering['component'], $ordering['direction']);
            }
        }

        return $isFiltered;
    }

    /**
     * @param ITableDisplay $table
     * @param string        $viewName
     *
     * @return ITableView
     */
    protected function loadTableView(ITableDisplay $table, string $viewName) : ITableView
    {
        try {
            return $table->getView($viewName);
        } catch (InvalidArgumentException $e) {
            DmsError::abort($request, 404);
        }
    }

    /**
     * @param IModule $module
     * @param string  $tableName
     *
     * @return array|ITableDisplay
     */
    protected function loadTable(IModule $module, string $tableName) : ITableDisplay
    {
        try {
            return $module->getTable($tableName);
        } catch (InvalidArgumentException $e) {
            $response = new JsonResponse([
                'message' => 'Invalid table name',
            ], 404);
        }

        return $response;
    }
}
