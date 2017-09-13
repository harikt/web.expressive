Dms.form.validation.initialize = function (form) {
    form.attr('data-parsley-validate', '1');
    return form.parsley(window.ParsleyConfig);
};

Dms.form.validation.clearMessages = function (form) {
    form.find('.form-group').removeClass('has-error');
    form.find('.help-block.help-block-error').remove();
};

Dms.form.validation.displayMessages = function (form, fieldMessages, generalMessages) {
    if (!fieldMessages && !generalMessages) {
        return;
    }

    var makeHelpBlock = function () {
        return $('<div />').addClass('help-block help-block-error');
    };

    var helpBlock = makeHelpBlock();

    $.each(generalMessages, function (index, message) {
        helpBlock.append($('<strong />').text(message));
    });

    form.prepend(helpBlock);

    var flattenedFieldMessages = {};

    var visitMessages = function (fieldName, messages) {
        if ($.isArray(messages)) {
            $.each(messages, function (index, message) {
                if (typeof flattenedFieldMessages[fieldName] === 'undefined') {
                    flattenedFieldMessages[fieldName] = [];
                }

                flattenedFieldMessages[fieldName].push(message);
            });
        } else {
            $.each(messages.constraints, function (index, message) {
                if (typeof flattenedFieldMessages[fieldName] === 'undefined') {
                    flattenedFieldMessages[fieldName] = [];
                }

                flattenedFieldMessages[fieldName].push(message);
            });

            $.each(messages.fields, function (fieldElementName, elementMessages) {
                visitMessages(fieldName + '[' + fieldElementName + ']', elementMessages);
            });
        }
    };
    $.each(fieldMessages, visitMessages);

    $.each(flattenedFieldMessages, function (fieldName, messages) {
        var fieldGroup = form.find('.form-group[data-field-name="' + fieldName + '"]').add(
            form.find('.form-group *[data-field-validation-for]')
                .filter(function () {
                    return $(this).attr('data-field-validation-for').indexOf(fieldName) !== -1;
                })
                .closest('.form-group')
        );

        var validationMessagesContainer = fieldGroup.find('.dms-validation-messages-container')
            .filter(function () {
                return $(this).closest('.form-group').is(fieldGroup);
            });

        var helpBlock = makeHelpBlock();
        $.each($.unique(messages), function (index, message) {
            helpBlock.append($('<p/>').append($('<strong />').text(message)));
        });

        fieldGroup.addClass('has-error');
        validationMessagesContainer.prepend(helpBlock);
    });
};