<?php /** @var \Dms\Core\Module\IChartView $chart */ ?>
<div
        class="dms-chart-control"
        data-load-chart-url="{{ $loadChartDataUrl }}"
>
    <div class="dms-chart-container" @if ($dateAxisName ?? false) data-date-axis-name="{{ $dateAxisName }}" @endif>
        <div class="dms-chart"></div>

        @if ($dateFormat ?? false)
            <hr>

            <div class="row">
                <div class="col-sm-2 col-lg-1"><label for="chart-{{ md5($loadChartDataUrl) }}">Filter: </label></div>
                <div class="col-sm-10 col-lg-11">
                    <div class='dms-chart-range-picker input-group dms-date-or-time-range' data-mode="{{ $dateMode }}" data-dont-auto-apply="1">
                        <input
                                id="chart-{{ md5($loadChartDataUrl) }}"
                                type="text"
                                class="form-control dms-start-input"
                                placeholder="Start"
                                data-date-format="{{ $dateFormat }}"
                        />
                        <span class="input-group-addon">to</span>
                        <input
                                type="text"
                                class="form-control dms-end-input"
                                placeholder="End"
                                data-date-format="{{ $dateFormat }}"
                        />
                    <span class="input-group-addon" onclick="$(this).prev('input').focus()">
                        <span class="fa fa-calendar"></span>
                    </span>
                    </div>
                </div>
            </div>
        @endif

        @include('dms::partials.spinner')
    </div>
</div>