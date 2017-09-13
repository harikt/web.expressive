Dms.chart.initializeCallbacks.push(function (element) {
    element.find('.dms-graph-chart').each(function () {
        var chart = $(this);
        var dateFormat = Dms.utilities.convertPhpDateFormatToMomentFormat(chart.attr('data-date-format'));
        var chartData = JSON.parse(chart.attr('data-chart-data'));
        var chartType = chart.attr('data-chart-type');
        var horizontalAxisKey = chart.attr('data-horizontal-axis-key');
        var horizontalAxisUnitType = chart.attr('data-horizontal-unit-type');
        var verticalAxisKeys = JSON.parse(chart.attr('data-vertical-axis-keys'));
        var verticalAxisLabels = JSON.parse(chart.attr('data-vertical-axis-labels'));
        var minTimestamp;
        var maxTimestamp;
        var timeRowLookup = {};

        if (!chart.attr('id')) {
            chart.attr('id', Dms.utilities.idGenerator());
        }

        $.each(chartData, function (index, row) {
            var timestamp = moment(row[horizontalAxisKey], dateFormat).valueOf();
            row[horizontalAxisKey] = timestamp;
            timeRowLookup[timestamp] = true;

            if (!minTimestamp || timestamp < minTimestamp) {
                minTimestamp = timestamp;
            }

            if (!maxTimestamp || timestamp > maxTimestamp) {
                maxTimestamp = timestamp;
            }
        });

        var zeroFillMissingValues = function (unitType, chartData) {
            if (chartData.length === 0) {
                return;
            }

            var addUnitToDate;
            if (unitType === 'date') {
                addUnitToDate = function (date) {
                    date.setDate(date.getDate() + 1);
                };
            } else {
                addUnitToDate = function (date) {
                    date.setSeconds(date.getSeconds() + 1)
                };
            }

            for (var i = new Date(minTimestamp); i.getTime() < maxTimestamp; addUnitToDate(i)) {
                if (typeof timeRowLookup[i.getTime()] === 'undefined') {
                    var rowData = {};
                    rowData[horizontalAxisKey] = i.getTime();

                    $.each(verticalAxisKeys, function (index, verticalAxisKey) {
                        rowData[verticalAxisKey] = 0;
                    });

                    chartData.push(rowData);
                }
            }
        };

        zeroFillMissingValues(horizontalAxisUnitType, chartData);

        var morrisConfig = {
            element: chart.attr('id'),
            data: chartData,
            xkey: horizontalAxisKey,
            ykeys: verticalAxisKeys,
            labels: verticalAxisLabels,
            resize: true,
            redraw: true,
            dateFormat: function (timestamp) {
                return moment(timestamp).format(dateFormat);
            }
        };

        var morrisChart;
        if (chartType === 'bar') {
            morrisChart = Morris.Bar(morrisConfig);
        } else if (chartType === 'area') {
            morrisChart = Morris.Area(morrisConfig);
        } else {
            morrisChart = Morris.Line(morrisConfig);
        }

        $(window).on('resize', function () {
            if (morrisChart.raphael) {
                morrisChart.redraw();
            }
        });
    });
});