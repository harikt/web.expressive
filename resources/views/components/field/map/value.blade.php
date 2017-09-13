<div class="dms-map-display">
    @if ($address ?? false)
        <div>{{ $address }}</div>
    @endif
    @if($latLng ?? false)
        <div>
            <div class="dms-display-map" data-latitude="{{ $latLng['lat'] }}" data-longitude="{{ $latLng['lng'] }}"></div>
        </div>
    @endif
</div>