<?php /** @var \Dms\Web\Expressive\Renderer\Form\FormRenderingContext $renderingContext */ ?>
<?php /** @var \Dms\Web\Expressive\Renderer\Form\IFieldRenderer $columnFieldRenderer */ ?>
<?php /** @var \Dms\Web\Expressive\Renderer\Form\IFieldRenderer|null $rowFieldRenderer */ ?>
<?php /** @var \Dms\Web\Expressive\Renderer\Form\IFieldRenderer $cellFieldRenderer */ ?>
<?php /** @var \Dms\Core\Form\IField $columnField */ ?>
<?php /** @var \Dms\Core\Form\IField|null $rowField */ ?>
<?php /** @var \Dms\Core\Form\IField $cellField */ ?>
<?php $columnField = $columnField->withName('', 'Column'); ?>
<?php $rowField = $rowField ? $rowField->withName('', 'Row') : []; ?>
<?php $cellField = $cellField->withName('', ' '); ?>
<table
        class="table table-bordered dms-field-table"
        data-field-validation-for="{{ $name }}[columns], {{ $name }}[rows], {{ $name }}[cells]"

        @if($exactColumns !== null)
        data-min-columns="{{ $exactColumns }}"
        data-max-columns="{{ $exactColumns }}"
        @else
        @if($minColumns !== null) data-min-columns="{{ $minColumns }}" @endif
        @if($maxColumns !== null) data-max-columns="{{ $maxColumns }}" @endif
        @endif

        @if($exactRows !== null)
        data-min-rows="{{ $exactRows }}"
        data-max-rows="{{ $exactRows }}"
        @else
        @if($minRows !== null) data-min-rows="{{ $minRows }}" @endif
        @if($maxRows !== null) data-max-rows="{{ $maxRows }}" @endif
        @endif

        @if($predefinedColumns !== null) data-has-predefined-columns="1" @endif
        @if($predefinedRows !== null) data-has-predefined-rows="1" @endif

        @if($rowField !== null) data-has-row-field="1" @endif
>
    <tr class="hidden dms-no-validation dms-form-no-submit">
        @if($predefinedColumns === null)
            <th class="table-column field-column-template">
                <div class="field-content">
                    {{ $columnFieldRenderer->render($renderingContext, $columnField->withName($name . '[columns][::column::]')->withInitialValue(null)) }}
                </div>
            </th>
        @endif
        @if($predefinedRows === null && $rowFieldRenderer !== null)
            <th class="field-row-template">
                <div class="field-content">
                    {{ $rowFieldRenderer->render($renderingContext, $rowField->withName($name . '[rows][::row::]')->withInitialValue(null)) }}
                </div>
            </th>
        @endif
        <td class="field-cell-template">
            {{ $cellFieldRenderer->render($renderingContext, $cellField->withName($name . '[cells][::row::][::column::]')->withInitialValue(null)) }}
        </td>
        <td class="remove-column-template">
            <button type="button" class="btn btn-danger btn-xs btn-block btn-remove-column"><i class="fa fa-times"></i></button>
        </td>
        <td class="remove-row-template add-column">
            <button type="button" class="btn btn-danger btn-sm btn-block btn-remove-row"><i class="fa fa-times"></i></button>
        </td>
    </tr>

    <thead>
    <tr>
        @if($rowFieldRenderer !== null)
            <th class="row-key-column">

            </th>
        @endif
        @if($predefinedColumns !== null)
            @foreach ($predefinedColumns as $key => $columnValue)
                <th class="table-column">
                    <div class="field-content">
                        {!! $columnFieldRenderer->renderValue($renderingContext, $columnField, $columnField->unprocess($columnValue)) !!}
                    </div>
                </th>
            @endforeach
        @elseif ($value !== null)
            @foreach ($value['columns'] as $key => $columnValue)
                <th class="table-column">
                    <div class="field-content">
                        {!! $columnFieldRenderer->render($renderingContext, $columnField->withName($name . '[columns][' . $key . ']')->withInitialValue($columnValue)) !!}
                    </div>
                </th>
            @endforeach
        @endif
        <th class="add-column">
            <button type="button" class="btn btn-success btn-sm btn-block btn-add-column"><i class="fa fa-plus"></i></button>
        </th>
    </tr>
    </thead>
    <tbody>
    @if ($value !== null)
        @foreach ($value['cells'] as $rowKey => $rowCellValues)
            <tr class="table-row">
                @if($predefinedRows !== null)
                    <th class="row-key-column">
                        <div class="field-content">
                            {!! $rowFieldRenderer->renderValue($renderingContext, $rowField, $rowField->unprocess($predefinedRows[$rowKey])) !!}
                        </div>
                    </th>
                @elseif ($rowFieldRenderer !== null)
                    <th class="row-key-column">
                        <div class="field-content">
                            {!! $rowFieldRenderer->render($renderingContext, $rowField->withName($name . '[rows][' . $rowKey . ']')->withInitialValue($value['rows'][$rowKey])) !!}
                        </div>
                    </th>
                @endif

                @foreach ($rowCellValues as $columnKey => $cellValue)
                    <td>
                        {!! $cellFieldRenderer->render($renderingContext, $cellField->withName($name . '[cells][' . $rowKey . '][' . $columnKey . ']')->withInitialValue($cellValue)) !!}
                    </td>
                @endforeach
                <td class="add-column">
                    <button type="button" class="btn btn-danger btn-sm btn-block btn-remove-row"><i class="fa fa-times"></i></button>
                </td>
            </tr>
        @endforeach
    @endif
    <tr class="add-row">
        @if($rowFieldRenderer !== null)
            <td>
                <button type="button" class="btn btn-success btn-xs btn-block btn-add-row"><i class="fa fa-plus"></i></button>
            </td>
        @endif
        @if($predefinedColumns !== null)
            @foreach ($predefinedColumns as $key => $columnValue)
                <td>

                </td>
            @endforeach
        @elseif ($value !== null)
            @foreach ($value['columns'] as $key => $columnValue)
                <td>
                    <button type="button" class="btn btn-danger btn-xs btn-block btn-remove-column"><i class="fa fa-times"></i></button>
                </td>
            @endforeach
            <td class="add-column">
            </td>
        @endif
    </tr>
    @if($rowFieldRenderer === null)
        <tr class="final-add-row">
            <td colspan="999">
                <button type="button" class="btn btn-success btn-xs btn-block btn-add-row"><i class="fa fa-plus"></i></button>
            </td>
        </tr>
    @endif
    </tbody>
</table>