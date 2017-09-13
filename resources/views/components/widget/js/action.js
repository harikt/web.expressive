Dms.widget.initializeCallbacks.push(function () {
    $('.dms-widget-unparameterized-action, .dms-widget-parameterized-action').each(function () {
        var widget = $(this);
        var button = widget.find('button');

        if (button.is('.btn-danger')) {
            var isConfirmed = false;

            button.click(function () {
                if (isConfirmed) {
                    isConfirmed = false;
                    return;
                }

                swal({
                    title: "Are you sure?",
                    text: "This will execute the '" + widget.attr('data-action-label') + "' action",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes proceed!"
                }, function () {
                    isConfirmed = true;
                    $(this).click();
                });

                return false;
            });
        }
    });
});