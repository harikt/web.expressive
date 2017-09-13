Dms.form.initializeCallbacks.push(function (element) {

    element.find('.dms-staged-form, .dms-run-action-form').each(function () {
        var form = $(this);
        var formContainer = form.closest('.dms-staged-form-container');
        var parsley = Dms.form.validation.initialize(form);
        var afterRunCallbacks = [];
        var submitButtons = form.find('input[type=submit], button[type=submit]');
        var submitMethod = form.attr('data-method');
        var submitUrl = form.attr('data-action');
        var reloadFormUrl = form.attr('data-reload-form-url');
        var shouldReloadPageAfterSubmit = form.attr('data-reload-page-after-submit');

        if ($(this).is('a.dms-run-action-form, button.dms-run-action-form')) {
            submitButtons = submitButtons.add(this);
        }

        var isFormValid = function () {
            return parsley.isValid()
                && form.find('.dms-validation-message *').length === 0
                && form.find('.dms-form-stage-container').length === form.find('.dms-form-stage-container.loaded').length;
        };

        submitButtons.on('click before-confirmation', function (e) {
            parsley.validate();

            if (!isFormValid()) {
                e.stopImmediatePropagation();
                form.find('.dms-form-stage-container:not(.loaded)').addClass('has-error');
                return false;
            }
        });

        submitButtons.on('click', function (e) {
            e.preventDefault();

            Dms.form.validation.clearMessages(form);

            form.triggerHandler('dms-before-submit');

            var fieldsToReappend = [];
            form.find('.dms-form-no-submit').each(function () {
                var removedFields = $(this).children().detach();

                fieldsToReappend.push({
                    parentElement: $(this),
                    children: removedFields
                });
            });

            var formData = Dms.form.stages.createFormDataFromFields(form.find(':input'));
            form.find('.form-group').each(function () {
                var additionalDataToSubmit = $(this).triggerHandler('dms-get-input-data');

                if (additionalDataToSubmit) {
                    $.each(Dms.ajax.parseData(additionalDataToSubmit), function (name, entries) {
                        $.each(entries, function (index, entry) {
                            formData.append(name, entry.value, entry.filename);
                        });
                    });
                }
            });

            $.each(fieldsToReappend, function (index, elements) {
                elements.parentElement.append(elements.children);
            });

            submitButtons.prop('disabled', true);
            submitButtons.addClass('ladda-button').attr('data-style', 'expand-right');
            var ladda = Ladda.create(submitButtons.get(0));
            ladda.start();

            var currentAjaxRequest = Dms.ajax.createRequest({
                url: submitUrl,
                type: submitMethod,
                processData: false,
                contentType: false,
                dataType: 'json',
                data: formData,
                xhr: function () {
                    var xhr = $.ajaxSettings.xhr();

                    if (form.find('input[type=file]').length && xhr.upload) {
                        xhr.upload.addEventListener('progress', function (event) {
                            if (event.lengthComputable) {
                                ladda.setProgress(event.loaded / event.total);
                            }
                        }, false);
                    }

                    return xhr;
                }
            });

            currentAjaxRequest.done(function (data, statusText, xhr) {
                Dms.action.responseHandler(xhr.status, submitUrl, data);
                $.each(afterRunCallbacks, function (index, callback) {
                    callback(data);
                });

                form.triggerHandler('dms-post-submit-success');
            });

            currentAjaxRequest.fail(function (xhr) {
                if (currentAjaxRequest.statusText === 'abort') {
                    return;
                }

                switch (xhr.status) {
                    case 401: // Unauthorized
                        Dms.controls.showErrorDialog({
                            title: "Could not perform action",
                            text: "You do not possess the necessary permissions to authorize this action",
                            type: "error"
                        });
                        break;

                    case 422: // Unprocessable Entity (validation failure)
                        var validation = JSON.parse(xhr.responseText);
                        Dms.form.validation.displayMessages(form, validation.messages.fields, validation.messages.constraints);
                        Dms.utilities.scrollToView(form.find('.help-block-error:not(:empty)').first());
                        break;

                    default:
                        try {
                            var response = JSON.parse(xhr.responseText);
                            Dms.action.responseHandler(xhr.status, submitUrl, response);
                        } catch (e) {
                            // Unknown error
                            Dms.controls.showErrorDialog({
                                title: "Could not submit form",
                                text: "An unexpected error occurred",
                                type: "error",
                                debugInfo: xhr.responseText
                            });
                            break;
                        }
                }
            });

            currentAjaxRequest.always(function () {
                submitButtons.prop('disabled', false);
                ladda.stop();
            });

            return false;
        });

        var parentToRemove = form.attr('data-after-run-remove-closest');
        if (parentToRemove) {
            afterRunCallbacks.push(function () {
                form.closest(parentToRemove).fadeOut(100);
            });
        }

        afterRunCallbacks.push(function () {
            form.find('input[type=password]').val('');
        });

        afterRunCallbacks.push(function (data) {
            if (data.redirect) {
                return;
            }

            if (shouldReloadPageAfterSubmit) {
                Dms.link.reloadCurrentPage();
                return;
            }

            if (!form.is('.dms-staged-form')) {
                return;
            }

            var request = Dms.ajax.createRequest({
                url: reloadFormUrl,
                type: 'get',
                dataType: 'html',
                data: {'__content_only': '1'}
            });

            formContainer.addClass('loading');

            request.done(function (html) {
                var newForm = $(html).find('.dms-staged-form').first();
                form.replaceWith(newForm);
                Dms.form.initialize(newForm.parent());
                Dms.table.initialize(newForm.parent());
            });

            request.always(function () {
                formContainer.removeClass('loading');
            });
        });
    });
});