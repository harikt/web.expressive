<input
        type="text"
        class="form-control dms-colour-input dms-colour-input-rgba"
        name="{{ $name }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($readonly) readonly @endif
        @if($value !== null) value="{{ $value }}" @endif
/>