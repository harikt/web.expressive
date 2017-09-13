Dms.controls.showContentDialog = function (title, content, showInIframe) {
    var contentDialog = $('.dms-content-dialog').first();

    contentDialog.find('.modal-title').text(title || '');

    var dialogBody = contentDialog.find('.modal-body');
    dialogBody.empty();

    if (showInIframe) {
        var iframe = $('<iframe />');
        iframe.addClass('dms-content-iframe');
        dialogBody.append(iframe);
        setTimeout(function () {
            var document = iframe.contents().get(0);
            document.open();
            document.write(content);
            document.close();
        }, 1);
    } else {
        dialogBody.html(content);
    }

    contentDialog.appendTo('body').modal('show');
    Dms.all.initialize(dialogBody);

    dialogBody.on('click', 'a[href]', function () {
        contentDialog.modal('hide');
    });
};