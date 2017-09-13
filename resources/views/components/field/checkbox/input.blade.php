<input
        type="checkbox"
        value="1"
        class="single-checkbox"
        name="{{ $name }}"
        @if($required) required @endif
        @if($readonly) readonly @endif
        @if($value) checked="checked" @endif
/>