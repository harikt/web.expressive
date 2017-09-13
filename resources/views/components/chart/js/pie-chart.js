Dms.chart.initializeCallbacks.push(function (element) {
    element.find('.dms-pie-chart').each(function () {
        var chart = $(this);
        var chartData = JSON.parse(chart.attr('data-chart-data'));

        if (!chart.attr('id')) {
            chart.attr('id', Dms.utilities.idGenerator());
        }

        var morrisChart = Morris.Donut({
            element: chart.attr('id'),
            data: chartData,
            resize: true,
            redraw: true
        });

        $(window).on('resize', function () {
            if (morrisChart.raphael) {
                morrisChart.redraw();
            }
        });
    });
});