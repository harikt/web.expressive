Dms.form.initializeCallbacks.push(function (element) {

    element.find('.list-of-checkboxes').each(function () {
        var listOfCheckboxes = $(this);
        listOfCheckboxes.find('input[type=checkbox]').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            increaseArea: '20%'
        });

        var firstCheckbox = listOfCheckboxes.find('input[type=checkbox]').first();
        firstCheckbox.attr('data-parsley-min-elements', listOfCheckboxes.attr('data-min-elements'));
        firstCheckbox.attr('data-parsley-max-elements', listOfCheckboxes.attr('data-max-elements'));
    });
});