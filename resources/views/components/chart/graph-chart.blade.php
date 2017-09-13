<div
        class="dms-graph-chart"
        data-chart-type="{{ $chartType }}"
        data-date-format="{{ $dateFormat }}"
        data-chart-data="{{ json_encode($data) }}"
        data-horizontal-axis-key="{{ $horizontalAxisKey }}"
        data-vertical-axis-keys="{{ json_encode($verticalAxisKeys) }}"
        data-vertical-axis-labels="{{ json_encode($verticalAxisLabels) }}"
        data-horizontal-unit-type="{{ $horizontalUnitType }}"
></div>