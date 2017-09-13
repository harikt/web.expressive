<div class="dms-map-input"
     data-input-mode="{{ $inputMode }}"
     data-name="{{ $name }}"
     data-default-latitude="{{ config('dms.localisation.form.defaults.map')[0] ?? null }}"
     data-default-longitude="{{ config('dms.localisation.form.defaults.map')[1] ?? null }}"
>
    <div>
        <div class="input-group">
            <input type="text"
                   class="dms-address-search form-control"
                   placeholder="{{ $placeholder }}"
                   @if($required ?? false) required @endif
                   @if($readonly ?? false) readonly @endif
            />
            <span class="input-group-btn">
                <button type="button" class="dms-current-location btn btn-default">
                    <span class="hidden-xs hidden-sm">Current location</span>
                    <span class="fa fa-map-marker"></span>
                </button>
            </span>
        </div>

        <div class="hidden">
            @if ($inputMode === 'lat-lng')
                <input type="hidden" name="{{ $name }}[lat]" class="dms-lat-input" @if($value !== null) value="{{ $value['lat'] }}" @endif>
                <input type="hidden" name="{{ $name }}[lng]" class="dms-lng-input" @if($value !== null) value="{{ $value['lng'] }}" @endif>
            @endif
            @if ($inputMode === 'address')
                <input type="hidden" name="{{ $name }}" class="dms-full-address-input" @if($value !== null) value="{{ $value }}" @endif>
            @endif
            @if ($inputMode === 'address-with-lat-lng')
                <input type="hidden" name="{{ $name }}[address]" class="dms-full-address-input" @if($value !== null) value="{{ $value['address'] }}" @endif>
                <input type="hidden" name="{{ $name }}[coordinates][lat]" class="dms-lat-input" @if($value !== null) value="{{ $value['coordinates']['lat'] }}" @endif>
                <input type="hidden" name="{{ $name }}[coordinates][lng]" class="dms-lng-input" @if($value !== null) value="{{ $value['coordinates']['lng'] }}" @endif>
            @endif
        </div>
    </div>
    <div>
        <div class="dms-map-picker"></div>
    </div>
</div>