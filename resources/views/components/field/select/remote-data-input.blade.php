<div class="dms-select-with-remote-data"
     data-remote-options-url="{{ $remoteDataUrl }}"
     data-remote-min-chars="{{ $remoteMinChars }}"
>
        <input
                type="hidden"
                class="form-control dms-select-hidden-input"
                name="{{ $name }}"
                @if($option !== null) value="{{ $option->getValue() }}" @endif
        />
        <input
                type="text"
                class="form-control dms-select-input"
                placeholder="{{ $placeholder }}"
                @if($required) required @endif
                @if($readonly) readonly @endif
                @if($option !== null) value="{{ $option->getLabel() }}" @endif
        />
</div>