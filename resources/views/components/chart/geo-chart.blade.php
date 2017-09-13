<div
        class="dms-geo-chart"
        data-city-chart="{{ $cityChart }}"
        data-location-label="{{ $locationLabel }}"
        data-value-labels="{{ json_encode($valueLabels) }}"
        @if($region !== null)
        data-region="{{ $region }}"
        @endif
        data-has-lat-lng="{{ $hasLatLng }}"
        data-chart-data="{{ json_encode($data) }}"
></div>