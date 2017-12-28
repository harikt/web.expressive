<?php /** @var \Dms\Core\Table\IColumn[] $columns */ ?>
<?php /** @var \Dms\Core\Table\ITableDataSource $tableDataSource */ ?>
<?php /** @var \Dms\Core\Module\ITableView $table */ ?>
<div
        class="dms-table-control clearfix"
        data-load-rows-url="{{ $serverUrlHelper->generate($loadRowsUrl) }}"
        @if ($reorderRowActionUrl)
        data-reorder-row-action-url="{{ $serverUrlHelper->generate($reorderRowActionUrl) }}"
        @endif
        data-string-filterable-component-ids="{{ json_encode($stringFilterableComponentIds) }}"
>
    <div class="row form-inline">

        <div class="col-md-4 col-lg-2 clearfix dms-table-rows-per-page-form">
            <div class="input-group">
                <span class="input-group-addon">Items Per Page</span>

                <div class="form-group">
                    <select name="items_per_page" class="form-control">
                        @foreach([25, 50, 100, 200, 1000] as $amount)
                            <option value="{{ $amount }}" @if($amount == ($defaultAmount ?? null)) selected @endif>{{ $amount }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <br class="visible-sm visible-xs">

        <div class="col-md-8 col-lg-10 clearfix  dms-table-quick-filter-form">
            <div class="input-group pull-right">
                <span class="input-group-addon">Order By</span>

                <div class="form-group">
                    <select name="component" class="form-control">
                        <option value="">Please select</option>
                        @foreach($columns as $column)
                            @continue($column->isHidden())

                            @foreach($column->getComponents() as $component)
                                @continue(!$tableDataSource->canUseColumnComponentInCriteria($column->getComponentId($component->getName())))

                                <option value="{{ $column->getName() . '.' . $component->getName() }}">
                                    @if($column->getLabel() === $component->getLabel())
                                        {{ $column->getLabel() }}
                                    @else
                                        {{ $column->getLabel() . ' > ' . $component->getLabel() }}
                                    @endif
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </div>

                <div class="form-group visible-lg-inline-block">
                    <select name="direction" class="form-control">
                        <option value="{{ \Dms\Core\Model\Criteria\OrderingDirection::ASC }}">
                            Asc
                        </option>
                        <option value="{{ \Dms\Core\Model\Criteria\OrderingDirection::DESC }}">
                            Desc
                        </option>
                    </select>
                </div>

                <span class="input-group-addon">Filter</span>

                <div class="form-group">
                    <input name="filter" class="form-control" type="text" placeholder="Filter"/>
                </div>

                <span class="input-group-btn">
                    <button class="btn btn-info" type="button"><i class="fa fa-search"></i></button>
                </span>
            </div>
        </div>
    </div>

    <hr/>

    <div class="dms-table-container clearfix">
        <table class="dms-table table table-hover"></table>
        @include('dms::partials.spinner')
    </div>

    <div class="dms-table-pagination">
        <nav>
            <ul class="pager">
                <li><a href="javascript:void(0)" class="dms-pagination-previous">Previous</a></li>
                <li><a href="javascript:void(0)" class="dms-pagination-next">Next</a></li>
            </ul>
        </nav>
    </div>
</div>
