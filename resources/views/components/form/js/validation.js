Dms.form.initializeValidationCallbacks.push(function (element) {

    element.find('.dms-form-fields').each(function () {
        if (!$(this).attr('id')) {
            $(this).attr('id', Dms.utilities.idGenerator());
        }
    });

    element.find('.dms-form-fields').each(function () {
        var formFieldSection = $(this);
        var formFieldsGroupId = formFieldSection.attr('id');


        var buildElementSelector = function (fieldName) {
            return '#' + formFieldsGroupId + ' *[name="' + fieldName + '"]';
        };

        var fieldValidations = {
            'data-equal-fields': 'data-parsley-equalto',
            'data-greater-than-fields': 'data-parsley-gt',
            'data-greater-than-or-equal-fields': 'data-parsley-gte',
            'data-less-than-fields': 'data-parsley-lt',
            'data-less-than-or-equal-fields': 'data-parsley-lte'
        };

        $.each(fieldValidations, function (validationAttr, parsleyAttr) {
            var fieldsMap = formFieldSection.attr(validationAttr);

            if (fieldsMap) {
                $.each(JSON.parse(fieldsMap), function (fieldName, otherFieldName) {
                    var field = $(buildElementSelector(fieldName));
                    field.attr(parsleyAttr, buildElementSelector(otherFieldName));
                });
            }
        });
    });

    element.find('.dms-staged-form').each(function () {
        var form = $(this);
        var parsley = Dms.form.validation.initialize(form);

        form.find('.dms-form-fields').each(function (index) {
            $(this).find(':input').attr('data-parsley-group', 'validation-group-' + index);
        });
    });

    element.find('.dms-form').each(function () {
        var form = $(this);
        var parsley = Dms.form.validation.initialize(form);
    });
});