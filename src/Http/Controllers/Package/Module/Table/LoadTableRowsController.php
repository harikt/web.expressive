<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers\Package\Module\Table;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\ICms;
use Dms\Core\Model\Criteria\Condition\ConditionOperator;
use Dms\Core\Model\Criteria\OrderingDirection;
use Dms\Core\Module\IModule;
use Dms\Core\Module\ITableDisplay;
use Dms\Core\Module\ITableView;
use Dms\Core\Table\Criteria\RowCriteria;
use Dms\Core\Table\ITableStructure;
use Dms\Web\Expressive\Http\Controllers\DmsController;
use Dms\Web\Expressive\Http\Controllers\Package\Module\ModuleContextTrait;
use Dms\Web\Expressive\Http\CurrentModuleContext;
use Dms\Web\Expressive\Renderer\Table\TableRenderer;
use Interop\Http\Server\RequestHandlerInterface;
use Interop\Http\Server\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ResponseInterface; 
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Router\RouterInterface;

use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The table controller
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class LoadTableRowsController extends DmsController implements ServerMiddlewareInterface
{
    use ModuleContextTrait;

    /**
     * @var TableRenderer
     */
    protected $tableRenderer;

    /**
     * TableController constructor.
     *
     * @param ICms          $cms
     * @param TableRenderer $tableRenderer
     */
    public function __construct(
        ICms $cms,
        IAuthSystem $auth,
        TemplateRendererInterface $template,
        RouterInterface $router,
        TableRenderer $tableRenderer
    ) {
        parent::__construct($cms, $auth, $template, $router);
        $this->tableRenderer = $tableRenderer;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $tableName = $request->getAttribute('table');
        $viewName = $request->getAttribute('view');

        $moduleContext = $this->getModuleContext($request, $this->router, $this->cms);

        CurrentModuleContext::setInstance($moduleContext);

        $table = $this->loadTable($moduleContext->getModule(), $tableName);

        $tableView = $this->loadTableView($table, $viewName);

        $criteria = $tableView->getCriteriaCopy() ?: $table->getDataSource()->criteria()->loadAll();

        $isFiltered = $this->filterCriteriaFromRequest($request, $table->getDataSource()->getStructure(), $criteria);

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
            return $this->abort($request, 404);
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
