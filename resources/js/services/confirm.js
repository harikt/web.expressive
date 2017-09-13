Dms.form.initializeCallbacks.push(function () {
    var submitButtons = $('.dms-staged-form, .dms-run-action-form').find('[type=submit].btn-danger')
        .add('a.dms-run-action-form.btn-danger')
        .add('button.dms-run-action-form.btn-danger');

    submitButtons.click(function (e) {
        var button = $(this);

        var result = button.triggerHandler('before-confirmation');
        if (result === false) {
            e.stopImmediatePropagation();
            return false;
        }

        if (button.data('dms-has-confirmed')) {
            button.data('dms-has-confirmed', false);
            return;
        }

        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes!"
        }, function () {
            button.data('dms-has-confirmed', true);
            button.click();
        });

        e.stopImmediatePropagation();
        return false;
    });
});