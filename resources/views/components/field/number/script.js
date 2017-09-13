Dms.form.initializeCallbacks.push(function (element) {
    element.find('input[type="number"][data-max-decimal-places]').each(function () {
        $(this).attr('data-parsley-max-decimal-places', $(this).attr('data-max-decimal-places'));
    });

    element.find('input[type="number"][data-greater-than]').each(function () {
        $(this).attr('data-parsley-gt', $(this).attr('data-greater-than'));
    });

    element.find('input[type="number"][data-less-than]').each(function () {
        $(this).attr('data-parsley-lt', $(this).attr('data-less-than'));
    });

    element.find('input[type="number"]').each(function () {
        if ($(this).attr('data-decimal-number')) {
            $(this).attr({
                'type': $(this).attr('step') ? 'number' : 'text',
                'data-parsley-type': 'number'
            });
        } else {
            $(this).attr({
                'data-parsley-type': 'integer'
            });
        }
    });
});