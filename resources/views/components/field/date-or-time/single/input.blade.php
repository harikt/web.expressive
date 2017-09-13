<div class="dms-date-picker-container">
    <div class='input-group date'>
        <input
                type="text"
                class="form-control dms-date-or-time"
                name="{{ $name }}"
                placeholder="{{ $placeholder }}"
                @if($required) required @endif
                @if($readonly) readonly @endif
                @if($value !== null) value="{{ $value }}" @endif
                data-date-format="{{ $format }}"
                data-mode="{{ $mode }}"

                @if($min !== null) data-min-date="{{ $min->getTimestamp() * 1000 }}" @endif
                @if($max !== null) data-max-date="{{ $max->getTimestamp() * 1000 }}" @endif
        />
    <span class="input-group-addon" onclick="$(this).prev('input').focus()">
        <span class="fa fa-calendar"></span>
    </span>
    </div>
</div>