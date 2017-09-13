<?php /** @var \Dms\Web\Expressive\Renderer\Form\FormRenderingContext $renderingContext */ ?>
<?php /** @var \Dms\Web\Expressive\Renderer\Form\IFieldRenderer $columnFieldRenderer */ ?>
<?php /** @var \Dms\Web\Expressive\Renderer\Form\IFieldRenderer|null $rowFieldRenderer */ ?>
<?php /** @var \Dms\Web\Expressive\Renderer\Form\IFieldRenderer $cellFieldRenderer */ ?>
<?php /** @var \Dms\Core\Form\IField $columnField */ ?>
<?php /** @var \Dms\Core\Form\IField|null $rowField */ ?>
<?php /** @var \Dms\Core\Form\IField $cellField */ ?>
<?php $columnField = $columnField->withName('', ''); ?>
<?php $rowField = $rowField ? $rowField->withName('', '') : null; ?>
<?php $cellField = $cellField->withName('', ''); ?>
<table class="table table-bordered dms-display-table">
    <thead>
    <tr>
        @if($rowField !== null)
            <th class="row-key-column">

            </th>
        @endif
        @foreach ($value['columns'] as $key => $columnValue)
            <th>
                {!! $columnFieldRenderer->renderValue($renderingContext, $columnField, $columnValue)  !!}
            </th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach ($value['cells'] as $rowKey => $rowCellValues)
        <tr>
            @if ($rowField !== null)
                <th class="row-key-column">
                    {!! $rowFieldRenderer->renderValue($renderingContext, $rowField, $value['rows'][$rowKey]) !!}
                </th>
            @endif
            @foreach ($rowCellValues as $columnKey => $cellValue)
                <td>
                    {!! $cellFieldRenderer->renderValue($renderingContext, $cellField, $cellValue) !!}
                </td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>