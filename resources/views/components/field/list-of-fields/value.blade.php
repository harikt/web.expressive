<?php /** @var \Dms\Web\Expressive\Renderer\Form\FormRenderingContext $renderingContext */ ?>
<?php /** @var \Dms\Web\Expressive\Renderer\Form\IFieldRenderer $fieldRenderer */ ?>
<?php /** @var \Dms\Core\Form\IField $elementField */ ?>
<?php $elementField = $elementField->withName($name . '[]', $label); ?>
@if ($processedValue === null || count($processedValue) === 0)
    @include('dms::components.field.null.value')
@else
    <ul class="dms-display-list list-group">
        @foreach ($processedValue as $valueElement)
            <li class="list-group-item">{!! $fieldRenderer->renderValue($renderingContext, $elementField->withInitialValue($valueElement)) !!}</li>
        @endforeach
    </ul>
@endif
