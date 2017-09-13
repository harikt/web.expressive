<?php /** @var \Dms\Core\Form\IFieldOption[] $options */ ?>
<?php /** @var array $value */ ?>
<?php $valuesAsKeys = $value ? array_fill_keys($value, true) : []; ?>
<select
        name="{{ $name }}"
        @if($required) required @endif
        @if($readonly) readonly @endif
        multiple="multiple"
>
    @foreach ($options as $option)
        <option
                value="{{ $option->getValue() }}"
                @if (isset($valuesAsKeys[$option->getValue()]))selected="selected" @endif
        >{{ $option->getLabel() }}</option>
    @endforeach
</select>