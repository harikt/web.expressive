<?php /** @var \Dms\Core\Form\IFieldOptions $options */ ?>
<select class="form-control"
        name="{{ $name }}"
        @if($required) required @endif
        @if($readonly) readonly @endif
>
    <option value="">
        @if($required)
            Please Select...
        @else
            None
        @endif
    </option>
    @foreach ($options->getAll() as $option)
        <option
                value="{{ $option->getValue() }}"
                @if($option->isDisabled()) disabled="disabled" @endif
                @if (\Dms\Web\Expressive\Renderer\Form\ValueComparer::areLooselyEqual($option->getValue(), $value)) selected="selected" @endif
        >{{ $option->getLabel() }}</option>
    @endforeach
</select>