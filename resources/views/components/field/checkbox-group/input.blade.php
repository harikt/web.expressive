<?php /** @var \Dms\Core\Form\IFieldOption[] $options */ ?>
<?php /** @var array $value */ ?>
<?php $valuesAsKeys = $value ? array_fill_keys($value, true) : []; ?>

<div class="list-of-checkboxes"
     @if($exactElements !== null)
         data-min-elements="{{ $exactElements }}"
         data-max-elements="{{ $exactElements }}"
     @else
         @if($minElements !== null) data-min-elements="{{ $minElements }}" @endif
         @if($maxElements !== null) data-max-elements="{{ $maxElements }}" @endif
     @endif
>
    @if(count($options) > 0)
        <div class="row">
        @foreach ($options as $option)
            <div class="col-xs-12">
                <label @if($option->isDisabled()) class="dms-disabled-input" @endif>
                    <input
                            type="checkbox"
                            @if($option->isDisabled()) disabled="disabled" @endif
                            value="{{ $option->getValue() }}"
                            name="{{ $name }}[]"
                            @if($readonly) readonly @endif
                            @if(isset($valuesAsKeys[$option->getValue()])) checked="checked" @endif
                    />
                    {{ $option->getLabel() }}
                </label>
            </div>
        @endforeach
        </div>
    @else
        <p class="help-block">No options are available</p>
    @endif
</div>