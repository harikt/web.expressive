<?php /** @var \Dms\Web\Expressive\Http\ModuleContext $moduleContext */ ?>
<?php /** @var \Dms\Web\Expressive\Renderer\Table\TableRenderer $tableRenderer */ ?>
<?php /** @var \Dms\Core\Module\IModule $module */ ?>
<?php /** @var \Dms\Core\Module\IAction $generalActions */ ?>
<?php /** @var \Dms\Core\Common\Crud\Table\ISummaryTable $summaryTable */ ?>
<?php /** @var \Dms\Core\Module\ITableView[] $summaryTableViews */ ?>
<div class="row">
    <div class="col-xs-12">
        <div class="nav-tabs-custom dms-table-tabs">
            <ul class="nav nav-tabs">
                @foreach($summaryTableViews as $view)
                    <li class="{{ $activeViewName === $view->getName() ? 'active' : '' }}">
                        <a class="dms-table-tab-show-button" href="#summary-table-table-{{ $view->getName() }}" data-toggle="tab">
                            {{ $view->getLabel() }}
                            <span class="dms-row-amount">({{ $summaryTable->loadAmountOfRowsInView($view->getName()) }})</span>
                        </a>
                    </li>
                @endforeach
                @if($createActionName ?? false)
                    <li class="pull-right dms-general-action-container">
                        <button class="btn btn-success" data-a-href="{{ $moduleContext->getUrl('action.form', ['package' => $request->getAttribute('package'), 'module' => $request->getAttribute('module'), 'action' => $createActionName, 'object_id' => null]) }}">
                            Add <i class="fa fa-plus-circle"></i>
                        </button>
                    </li>
                @endif
                @foreach(array_reverse($generalActions) as $action)
                    <li class="pull-right dms-general-action-container">
                        @if($action instanceof \Dms\Core\Module\IUnparameterizedAction)
                            <button class="dms-run-action-form inline btn btn-{{ \Dms\Web\Expressive\Util\KeywordTypeIdentifier::getClass($action->getName()) }}"
                                 data-action="{{ $moduleContext->getUrl('action.run', ['package' => $moduleContext->getModule()->getPackageName(), 'module' => $moduleContext->getModule()->getName(), 'action' => $action->getName()]) }}"
                                 data-method="post"
                            >
                                {{ \Dms\Web\Expressive\Util\ActionLabeler::getActionButtonLabel($action)  }}
                            </button>
                        @else
                            <a  class="btn btn-{{ \Dms\Web\Expressive\Util\KeywordTypeIdentifier::getClass($action->getName()) }}"
                                href="{{ $moduleContext->getUrl('action.form', ['package' => $request->getAttribute('package'), 'module' => $request->getAttribute('module'), 'action' => $action->getName()]) }}"
                            >{{ \Dms\Web\Expressive\Util\ActionLabeler::getActionButtonLabel($action) }}</a>
                        @endif
                    </li>
                @endforeach
            </ul>
            <div class="tab-content">
                @foreach($summaryTableViews as $view)
                    <div class="tab-pane {{ $activeViewName === $view->getName() ? 'active' : '' }}" id="summary-table-table-{{ $view->getName() }}">
                        {!! $tableRenderer->renderTableControl($moduleContext, $summaryTable, $view->getName()) !!}
                    </div>
                    <!-- /.tab-pane -->
                @endforeach
            </div>
            <!-- /.tab-content -->
        </div>
        <!-- nav-tabs-custom -->
    </div>
    <!-- /.col -->
</div>
