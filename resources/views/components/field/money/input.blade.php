<div class="input-group dms-money-input-group" data-field-validation-for="{{ $name }}[amount], {{ $name }}[currency]">
    <span class="input-group-addon">$</span>
    <input
            type="text"
            class="form-control dms-money-input"
            name="{{ $name }}[amount]"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($readonly) readonly @endif
            @if($value !== null) value="{{ $value['amount'] }}" @endif
    />
    <select class="form-control dms-currency-input" name="{{ $name }}[currency]"
            @if($required) required @endif
            @if($readonly) readonly @endif
    >
        @foreach ($currencyOptions->getAll() as $option)
            <?php $currency = new \Dms\Common\Structure\Money\Currency($option->getValue()) ?>
            <option value="{{ $option->getValue() }}"
                    data-fractional-digits="{{ $currency->getDefaultFractionDigits() }}"
                    @if($value ? $option->getValue() === $value['currency'] : $option->getValue() === $defaultCurrency) selected="selected" @endif
            >
                {{ $option->getLabel() }}
            </option>
        @endforeach
    </select>
</div>