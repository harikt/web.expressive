Dms.controls.showErrorDialog = function (config) {
    if (Dms.config.debug && config.debugInfo) {

        var errorDialog = $('.dms-error-dialog').first();

        errorDialog.find('.modal-title').text(config.title || 'An error occurred');

        var dialogBody = errorDialog.find('.modal-body');
        dialogBody.empty();

        var iframe = $('<iframe />');
        iframe.addClass('dms-content-iframe');
        dialogBody.append(iframe);
        setTimeout(function () {
            var document = iframe.contents().get(0);
            document.open();
            document.write(config.debugInfo);
            document.close();
        }, 1);

        errorDialog.appendTo('body').modal('show');

        errorDialog.find('.dms-refresh-page-button').on('click', function () {
            window.location.reload();
        });

    } else {
        config = $.extend({}, config, {
            type: 'error'
        });

        swal(config);
    }
};