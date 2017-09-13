<input
        type="number"
        class="form-control"
        name="{{ $name }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($readonly) readonly @endif
        @if($value !== null) value="{{ $value }}" @endif

        @if(isset($min)) min="{{ $min }}" @endif
        @if(isset($max)) min="{{ $max }}" @endif
        @if(isset($greaterThan)) data-greater-than="{{ $greaterThan }}" @endif
        @if(isset($lessThan)) data-less-than="{{ $lessThan }}" @endif

        @if(isset($decimalNumber)) data-decimal-number="1" @endif
        @if(isset($maxDecimalPlaces)) step="{{ pow(.1, $maxDecimalPlaces) }}" data-max-decimal-places="{{ $maxDecimalPlaces }}" @endif
        @if(isset($decimalNumber) && !isset($maxDecimalPlaces)) step="any" @endif
/>