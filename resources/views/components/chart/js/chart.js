Dms.chart.initializeCallbacks.push(function (element) {

    element.find('.dms-chart-control').each(function () {
        var control = $(this);
        var chartContainer = control.find('.dms-chart-container');
        var chartElement = chartContainer.find('.dms-chart');
        var chartRangePicker = chartContainer.find('.dms-chart-range-picker');
        var loadChartUrl = control.attr('data-load-chart-url');

        var criteria = {
            orderings: [],
            conditions: []
        };

        var currentAjaxRequest;

        var loadCurrentData = function () {
            chartContainer.addClass('loading');

            if (currentAjaxRequest) {
                currentAjaxRequest.abort();
            }

            currentAjaxRequest = Dms.ajax.createRequest({
                url: loadChartUrl,
                type: 'post',
                dataType: 'html',
                data: criteria
            });

            currentAjaxRequest.done(function (chartData) {
                chartElement.html(chartData);
                Dms.chart.initialize(chartElement);
            });

            currentAjaxRequest.fail(function (response) {
                if (currentAjaxRequest.statusText === 'abort') {
                    return;
                }

                chartContainer.addClass('error');

                Dms.controls.showErrorDialog({
                    title: "Could not load chart data",
                    text: "An unexpected error occurred",
                    type: "error",
                    debugInfo: response.responseText
                });
            });

            currentAjaxRequest.always(function () {
                chartContainer.removeClass('loading');
            });
        };

        loadCurrentData();

        chartRangePicker.on('dms-range-updated', function () {
            var horizontalAxis = chartContainer.attr('data-date-axis-name');
            criteria.conditions = [
                {axis: horizontalAxis, operator: '>=', value: chartRangePicker.find('.dms-start-input').val()},
                {axis: horizontalAxis, operator: '<=', value: chartRangePicker.find('.dms-end-input').val()}
            ];

            loadCurrentData();
        });
    });
});