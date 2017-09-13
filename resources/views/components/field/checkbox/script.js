Dms.form.initializeCallbacks.push(function (element) {
    element.find('input[type=checkbox].single-checkbox').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        increaseArea: '20%'
    });

    element.find('input[type=checkbox]').each(function () {
        var formGroup = $(this).closest('.form-group');

        $(this).on('ifToggled', function(event){
            formGroup.trigger('dms-change');
        });
    });
});