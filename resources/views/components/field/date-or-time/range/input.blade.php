<div class='input-group dms-date-or-time-range' data-mode="{{ $mode }}">
    <input
            type="text"
            class="form-control dms-start-input"
            name="{{ $name }}[start]"
            placeholder="Start"
            @if($required) required @endif
            @if($readonly) readonly @endif
            @if($value !== null) value="{{ $value['start'] }}" @endif
            data-date-format="{{ $format }}"

            @if($min !== null) data-min-date="{{ $min->getTimestamp() * 1000 }}" @endif
            @if($max !== null) data-max-date="{{ $max->getTimestamp() * 1000 }}" @endif
    />
    <span class="input-group-addon">to</span>
    <input
            type="text"
            class="form-control dms-end-input"
            name="{{ $name }}[end]"
            placeholder="End"
            @if($required) required @endif
            @if($readonly) readonly @endif
            @if($value !== null) value="{{ $value['end'] }}" @endif
            data-date-format="{{ $format }}"

            @if($min !== null) data-min-date="{{ $min->getTimestamp() * 1000 }}" @endif
            @if($max !== null) data-max-date="{{ $max->getTimestamp() * 1000 }}" @endif
    />
    <span class="input-group-addon" onclick="$(this).prev('input').focus()">
        <span class="fa fa-calendar"></span>
    </span>
    @if(!$required)
        <span class="input-group-btn">
            <button class="btn btn-danger dms-btn-clear-input" type="button">
                <i class="fa fa-times"></i>
            </button>
          </span>
    @endif
</div>