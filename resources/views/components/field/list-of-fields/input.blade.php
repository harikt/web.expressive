<?php /** @var \Dms\Web\Expressive\Renderer\Form\FormRenderingContext $renderingContext */ ?>
<?php /** @var \Dms\Web\Expressive\Renderer\Form\IFieldRenderer $fieldRenderer */ ?>
<?php /** @var \Dms\Core\Form\IField $elementField */ ?>
<?php $elementField = $elementField->withName($name, str_singular($label)); ?>
<ul
        class="list-group dms-field-list"
        @if($exactElements !== null)
        data-min-elements="{{ $exactElements }}"
        data-max-elements="{{ $exactElements }}"
        @else
        @if($minElements !== null) data-min-elements="{{ $minElements }}" @endif
        @if($maxElements !== null) data-max-elements="{{ $maxElements }}" @endif
        @endif
>
    <li class="list-group-item hidden field-list-template clearfix dms-no-validation dms-form-no-submit">
        <div class="row">
            <div class="col-xs-8 col-md-10 field-list-input">
                {!! htmlspecialchars($fieldRenderer->render($renderingContext, $elementField->withName($name . '[::index::]')->withInitialValue(null))) !!}
            </div>
            <div class="col-xs-4 col-md-2 field-list-button-container">
                <div class="pull-right">
                    <button class="btn btn-danger dms-remove-field-button" tabindex="-1"><span class="fa fa-times"></span></button>
                    <button class="btn btn-success dms-reorder-field-button" tabindex="-1"><span class="fa fa-arrows"></span></button>
                </div>
            </div>
        </div>
    </li>

    @if ($value !== null)
        <?php $i = 0 ?>
        @foreach ($processedValue as $valueElement)
            <li class="list-group-item field-list-item clearfix">
                <div class="row">
                    <div class="col-xs-8 col-md-10 field-list-input">
                        {!! $fieldRenderer->render($renderingContext, $elementField->withName($name . '[' . $i . ']')->withInitialValue($valueElement)) !!}
                    </div>
                    <div class="col-xs-4 col-md-2 field-list-button-container">
                        <div class="pull-right">
                            <button class="btn btn-danger dms-remove-field-button" tabindex="-1"><span class="fa fa-times"></span></button>
                            <button class="btn btn-success dms-reorder-field-button" tabindex="-1"><span class="fa fa-arrows"></span></button>
                        </div>
                    </div>
                </div>
            </li>
            <?php $i++ ?>
        @endforeach
    @endif

    <li class="list-group-item field-list-add">
        <button type="button" class="btn btn-success btn-add-field">Add <span class="fa fa-plus"></span></button>
    </li>
</ul>
