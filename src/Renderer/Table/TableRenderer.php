<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Table;

use Dms\Core\Common\Crud\IReadModule;
use Dms\Core\Common\Crud\Table\ISummaryTable;
use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Model\Criteria\Condition\ConditionOperator;
use Dms\Core\Module\ITableDisplay;
use Dms\Core\Table\IDataTable;
use Dms\Core\Table\ITableDataSource;
use Dms\Web\Expressive\Http\ModuleContext;
use Dms\Web\Expressive\Renderer\Action\ActionButton;
use Dms\Web\Expressive\Renderer\Action\ObjectActionButtonBuilder;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The table renderer class.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TableRenderer
{
    /**
     * @var ColumnRendererFactoryCollection
     */
    protected $columnRendererFactories;

    /**
     * @var ObjectActionButtonBuilder
     */
    private $actionButtonBuilder;

    protected $template;

    /**
     * TableRenderer constructor.
     *
     * @param ColumnRendererFactoryCollection $columnRendererFactories
     * @param ObjectActionButtonBuilder       $actionButtonBuilder
     */
    public function __construct(
        ColumnRendererFactoryCollection $columnRendererFactories,
        ObjectActionButtonBuilder $actionButtonBuilder,
        TemplateRendererInterface $template
    ) {
        $this->columnRendererFactories = $columnRendererFactories;
        $this->actionButtonBuilder     = $actionButtonBuilder;
        $this->template = $template;
    }

    /**
     * Renders the supplied data table as a html string.
     *
     * @param ModuleContext       $moduleContext
     * @param ITableDisplay       $table
     * @param IDataTable          $tableData
     * @param string              $viewName
     * @param bool                $isFiltered
     * @param ActionButton[]|null $actionButtons
     *
     * @return string
     * @throws UnrenderableColumnComponentException
     * @throws \Exception
     * @throws \Throwable
     */
    public function renderTableData(
        ModuleContext $moduleContext,
        ITableDisplay $table,
        IDataTable $tableData,
        string $viewName = null,
        bool $isFiltered = false,
        array $actionButtons = null
    ) : string {
        $columnRenderers = [];

        foreach ($tableData->getStructure()->getColumns() as $column) {
            $columnRenderers[$column->getName()] = $this->columnRendererFactories->buildRendererFor($column);
        }

        $rowActionButtons = [];
        if ($actionButtons) {
            InvalidArgumentException::verifyAllInstanceOf(__METHOD__, 'actionButtons', $actionButtons, ActionButton::class);

            foreach ($actionButtons as $actionButton) {
                $rowActionButtons[$actionButton->getName()] = $actionButton;
            }
        } else {
            if ($moduleContext->getModule() instanceof IReadModule && $table instanceof ISummaryTable) {
                foreach ($this->actionButtonBuilder->buildActionButtons($moduleContext) as $actionButton) {
                    $rowActionButtons[$actionButton->getName()] = $actionButton;
                }
            }
        }

        return $this->template->render('dms::components.table.data-table', [
                'dataSource'       => $table->getDataSource(),
                'columns'          => $tableData->getStructure()->getColumns(),
                'columnRenderers'  => $columnRenderers,
                'sections'         => $tableData->getSections(),
                'rowActionButtons' => $rowActionButtons,
                'allowsReorder'    => !$isFiltered && $viewName && $this->allowsRowReorder($table, $viewName),
            ]);
    }

    /**
     * Renders the supplied table control as a html string.
     *
     * @param ModuleContext $moduleContext
     * @param ITableDisplay $table
     * @param string        $viewName
     *
     * @return string
     */
    public function renderTableControl(ModuleContext $moduleContext, ITableDisplay $table, string $viewName = null) : string
    {
        $viewName = $viewName ?? $table->getDefaultView()->getName();
        $columns  = $table->getDataSource()->getStructure()->getColumns();

        if ($moduleContext->getModule() instanceof IReadModule && $table instanceof ISummaryTable) {
            unset($columns[IReadModule::SUMMARY_TABLE_ID_COLUMN]);
        }

        if ($this->allowsRowReorder($table, $viewName)) {
            $reorderRowActionUrl = $moduleContext->getUrl('action.run', [$table->getReorderAction($viewName)->getName()]);
        } else {
            $reorderRowActionUrl = null;
        }

        $module = $moduleContext->getModule();

        return view('dms::components.table.table-control')
            ->with([
                'columns'                      => $columns,
                'tableDataSource'              => $table->getDataSource(),
                'table'                        => $table->getView($viewName),
                'loadRowsUrl'                  => $moduleContext->getUrl('table.view.load', ['package' => $module->getPackageName(), 'module' => $module->getName(), 'table' => $table->getName(), 'view' => $viewName]),
                'reorderRowActionUrl'          => $reorderRowActionUrl,
                'stringFilterableComponentIds' => $this->getStringFilterableColumnComponentIds($table->getDataSource()),
                'defaultAmount'                => $moduleContext->getModule()->getMetadata('default-items-per-page'),
            ])
            ->render();
    }

    protected function getStringFilterableColumnComponentIds(ITableDataSource $tableDataSource) : array
    {
        $componentIds = [];

        foreach ($tableDataSource->getStructure()->getColumns() as $column) {
            foreach ($column->getComponents() as $component) {
                $componentId = $column->getName() . '.' . $component->getName();
                if ($component->getType()->hasOperator(ConditionOperator::STRING_CONTAINS_CASE_INSENSITIVE)
                    && $tableDataSource->canUseColumnComponentInCriteria($componentId)
                ) {
                    $componentIds[] = $componentId;
                }
            }
        }

        return $componentIds;
    }

    /**
     * @param ITableDisplay $table
     * @param string        $viewName
     *
     * @return bool
     */
    protected function allowsRowReorder(ITableDisplay $table, string $viewName) : bool
    {
        return $table instanceof ISummaryTable
        && $table->hasReorderAction($viewName)
        && $table->getReorderAction($viewName)->isAuthorized();
    }
}
