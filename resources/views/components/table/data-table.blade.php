<?php /** @var \Dms\Core\Table\ITableDataSource $dataSource */ ?>
<?php /** @var \Dms\Core\Table\IColumn[] $columns */ ?>
<?php /** @var \Dms\Core\Table\ITableSection[] $sections */ ?>
<?php /** @var \Dms\Web\Expressive\Renderer\Table\IColumnRenderer[] $columnRenderers */ ?>
<?php /** @var \Dms\Web\Expressive\Renderer\Action\ActionButton[] $rowActionButtons */ ?>
<?php /** @var bool $allowsReorder */ ?>
<table class="table table-hover dms-table">
    @if (!$sections || !$sections[0]->hasGroupData())
        <thead>
        <tr>
            @foreach ($columns as $column)
                <th data-column-name="{{ $column->getName() }}"
                    @if($canBeOrdered = $column->hasSingleComponent() && $dataSource->canUseColumnComponentInCriteria($column->getComponentId()))
                    data-order="{{ $column->getComponentId() }}"
                    @endif
                    @if($column->isHidden()) class="hidden" @endif
                >
                    {!! $columnRenderers[$column->getName()]->renderHeader() !!}

                    @if($canBeOrdered)
                        <span class="dms-order-icons">
                            <i class="fa fa-sort"></i>
                            <i class="fa fa-sort-asc"></i>
                            <i class="fa fa-sort-desc"></i>
                        </span>
                    @endif
                </th>
            @endforeach
            @if($rowActionButtons)
                <th class="dms-row-action-column"><span class="pull-right">Actions</span></th>
            @endif
        </tr>
        </thead>
    @endif
    @forelse($sections as $section)
        @if ($section->hasGroupData())
            <?php $groupData = $section->getGroupData()->getData()?>
            <thead>
            <tr>
                <td colspan="{{ count($columns) + ($rowActionButtons || $allowsReorder ? 1 : 0) }}">
                    @foreach ($groupData as $columnName => $value)
                        <h4>
                            {{ $columns[$columnName]->getLabel() }}
                            : {!! $columnRenderers[$columnName]->render($value) !!}
                        </h4>
                    @endforeach
                </td>
            </tr>
            <tr>
                @foreach ($columns as $columnName => $column)
                    @unless($groupData[$columnName] ?? false)
                        <th data-column-name="{{ $column->getName() }}" @if($column->isHidden()) class="hidden" @endif>
                            {!! $columnRenderers[$column->getName()]->renderHeader() !!}
                        </th>
                    @endunless
                @endforeach
                @if($rowActionButtons)
                    <th class="dms-row-action-column"><span class="pull-right">Actions</span></th>
                @endif
            </tr>
            </thead>
        @endif

        <tbody class="@if($allowsReorder) dms-table-body-sortable @endif">
        <?php $newObjectsIndex = 0 ?>
        @forelse ($section->getRows() as $row)
            <?php $rowData = $row->getData() ?>
            <?php $object = $row instanceof \Dms\Core\Table\Data\Object\TableRowWithObject ? $row->getObject() : null ?>
            <tr>
                @foreach ($columns as $columnName => $column)
                    @unless($groupData[$columnName] ?? false)
                        <td data-column-name="{{ $columnName }}" @if($columns[$columnName]->isHidden()) class="hidden" @endif>
                            {!! $columnRenderers[$columnName]->render($rowData[$columnName]) !!}
                        </td>
                    @endunless
                @endforeach
                @if($rowActionButtons || $allowsReorder)
                    <?php $objectId = $row->getCellComponentData(\Dms\Core\Common\Crud\IReadModule::SUMMARY_TABLE_ID_COLUMN) ?>
                    <td class="dms-row-action-column" data-object-id="{{ $objectId }}">
                        <div class="dms-row-button-control pull-right">
                            @if(isset($rowActionButtons['details']))
                                <a href="{{ $serverUrlHelper->generate($rowActionButtons['details']->getUrl($objectId)) }}" title="View Details"
                                   class="btn btn-xs btn-info">
                                    <i class="fa fa-bars"></i>
                                </a>
                            @endif
                            @if(isset($rowActionButtons['edit']))
                                <a href="{{ $serverUrlHelper->generate($rowActionButtons['edit']->getUrl($objectId)) }}" title="Edit"
                                   class="btn btn-xs btn-primary">
                                    <i class="fa fa-pencil-square-o"></i>
                                </a>
                            @endif
                            @if(isset($rowActionButtons['remove']))
                                <div class="dms-run-action-form inline"
                                     data-action="{{ $serverUrlHelper->generate($rowActionButtons['remove']->getUrl($objectId)) }}"
                                     data-after-run-remove-closest="tr"
                                     data-method="post">
                                    <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                                    <button type="submit" class="btn btn-xs btn-danger">
                                        <i class="fa fa-trash-o"></i>
                                    </button>
                                </div>
                            @endif

                            <?php
                            $specificRowButtons = array_filter(
                            array_diff_key($rowActionButtons, ['details' => true, 'edit' => true, 'remove' => true]),
                            function ($action) use ($object) {
                                return !$object || $action->isSupported($object);
                            }
                            );
                            ?>
                            @if($specificRowButtons)
                                <div class="inline dropdown-container">
                                    <button type="button" class="btn btn-xs btn-default dropdown-toggle"
                                            data-toggle="dropdown"
                                            aria-expanded="false">
                                        &nbsp;<span class="fa fa-caret-down"></span>&nbsp;
                                    </button>
                                    <ul class="dropdown-menu  dropdown-menu-right">
                                        @foreach($specificRowButtons as $action)
                                            @if(!$object || $action->isSupported($object))
                                                <li>
                                                    @if($action->isPost())
                                                        <a class="dms-run-action-form inline"
                                                           data-action="{{ $serverUrlHelper->generate($action->getUrl($objectId)) }}"
                                                           data-method="post">
                                                            {{ $action->getLabel() }}
                                                        </a>
                                                    @else
                                                        <a href="{{ $serverUrlHelper->generate($action->getUrl($objectId)) }}">{{ $action->getLabel() }}</a>
                                                    @endif
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if ($allowsReorder)
                                <button title="Reorder" class="btn btn-xs btn-success dms-drag-handle">
                                    <i class="fa fa-arrows"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                @endif
            </tr>
    @empty
        <tbody>
        <tr>
            <td colspan="{{ count($columns) + ($rowActionButtons || $allowsReorder ? 1 : 0) }}">
                <div class="help-block text-center">There are no items</div>
            </td>
        </tr>
        </tbody>
    @endforelse
    @empty
        <tbody>
        <tr>
            <td colspan="{{ count($columns) + ($rowActionButtons || $allowsReorder ? 1 : 0) }}">
                <div class="help-block text-center">There are no items</div>
            </td>
        </tr>
        </tbody>
    @endforelse
</table>
