Dms.form.initializeCallbacks.push(function (element) {

    element.find('.dms-staged-form').each(function () {
        var form = $(this);
        var parsley = Dms.form.validation.initialize(form);
        var stageElements = form.find('.dms-form-stage');

        var arePreviousFieldsValid = function (fields) {
            var originalScroll = $(document).scrollTop();
            var focusedElement = $(document.activeElement);
            parsley.validate();
            focusedElement.focus();
            $(document).scrollTop(originalScroll);

            return fields.closest('.form-group').find('.dms-validation-message *').length === 0;
        };

        stageElements.filter('.dms-dependent-form-stage').each(function () {
            var currentStage = $(this);
            var container = currentStage.closest('.dms-form-stage-container');
            var previousStages = container.prevAll('.dms-form-stage-container').find('.dms-form-stage');
            var loadStageUrl = currentStage.attr('data-load-stage-url');
            var dependentFields = currentStage.attr('data-stage-dependent-fields-stage-map');
            var stageToDependentFieldsMap = dependentFields ? JSON.parse(currentStage.attr('data-stage-dependent-fields-stage-map')) : null;
            var currentAjaxRequest = null;
            var previousLoadAttempt = 0;
            var minMillisecondsBetweenLoads = 2000;
            var isWaitingForNextLoadAttempt = false;

            var makeDependentFieldSelectorFor = function (selector) {
                if (stageToDependentFieldsMap) {
                    return Dms.form.stages.makeDependentFieldSelectorForStageMap(stageToDependentFieldsMap, selector);
                } else {
                    return Dms.form.stages.makeDependentFieldSelectorFor(null, selector);
                }
            };

            var loadNextStage = function (event) {
                if (event && event.target) {
                    var formForEventTarget = $(event.target).closest('.dms-staged-form');

                    if (!formForEventTarget.is(form)) {
                        return;
                    }
                }

                if (currentAjaxRequest) {
                    currentAjaxRequest.abort();
                }

                if (stageToDependentFieldsMap) {
                    var hasLoadedAllRequiredFields = true;

                    $.each(stageToDependentFieldsMap, function (stageNumber, dependentFields) {
                        var stage = previousStages.filter('[data-stage-number=' + stageNumber + ']');

                        $.each(dependentFields, function (index, fieldName) {
                            if (stage.find('.form-group[data-field-name="' + fieldName + '"]').length === 0) {
                                hasLoadedAllRequiredFields = false;
                            }
                        });
                    });

                    if (!hasLoadedAllRequiredFields) {
                        return;
                    }
                }

                container.removeClass('loaded');
                container.addClass('loading');

                var currentTime = new Date().getTime();
                var millisecondsBetweenLastLoad = currentTime - previousLoadAttempt;

                if (millisecondsBetweenLastLoad >= minMillisecondsBetweenLoads) {
                    isWaitingForNextLoadAttempt = false;
                    previousLoadAttempt = currentTime;
                }
                else {
                    if (!isWaitingForNextLoadAttempt) {
                        isWaitingForNextLoadAttempt = true;
                        setTimeout(loadNextStage, minMillisecondsBetweenLoads - millisecondsBetweenLastLoad);
                    }
                    return;
                }

                var previousFields = form.find(makeDependentFieldSelectorFor('*'));

                if (!arePreviousFieldsValid(previousFields)) {
                    container.removeClass('loading');
                    return;
                }

                Dms.form.validation.clearMessages(form);

                var formData = Dms.form.stages.getDependentDataForStage(currentStage);

                currentAjaxRequest = Dms.ajax.createRequest({
                    url: loadStageUrl,
                    type: 'post',
                    processData: false,
                    contentType: false,
                    dataType: 'html',
                    data: formData
                });

                currentAjaxRequest.done(function (html) {
                    currentStage.triggerHandler('dms-stage-reload');
                    container.addClass('loaded');
                    var currentValues = currentStage.getValues(true);
                    currentStage.html(html);
                    Dms.form.initialize(currentStage);
                    Dms.table.initialize(currentStage);
                    currentStage.restoreValues(currentValues);
                    form.triggerHandler('dms-form-updated');
                });

                currentAjaxRequest.fail(function (xhr) {
                    if (currentAjaxRequest.statusText === 'abort') {
                        return;
                    }

                    switch (xhr.status) {
                        case 422: // Unprocessable Entity (validation failure)
                            var validation = JSON.parse(xhr.responseText);
                            Dms.form.validation.displayMessages(form, validation.messages.fields, validation.messages.constraints);
                            break;

                        case 400: // Bad request
                            Dms.controls.showErrorDialog({
                                title: "Could not load form",
                                text: JSON.parse(xhr.responseText).message,
                                type: "error"
                            });
                            break;

                        default: // Unknown error
                            Dms.controls.showErrorDialog({
                                title: "Could not load form",
                                text: "An unexpected error occurred",
                                type: "error",
                                debugInfo: xhr.responseText
                            });
                            break;
                    }
                });

                currentAjaxRequest.always(function () {
                    container.removeClass('loading');
                });
            };

            form.on('input', makeDependentFieldSelectorFor('input'), loadNextStage);
            form.on('input', makeDependentFieldSelectorFor('textarea'), loadNextStage);
            form.on('change', makeDependentFieldSelectorFor('select'), loadNextStage);

            if (stageToDependentFieldsMap) {
                var selectors = [];

                $.each(stageToDependentFieldsMap, function (stageNumber, dependentFields) {
                    var stage = previousStages.filter('[data-stage-number=' + stageNumber + ']');
                    $.each(dependentFields, function (index, fieldName) {
                        selectors.push('.dms-form-stage[data-stage-number=' + stageNumber + '] .form-group[data-field-name="' + fieldName + '"]');
                    });
                });

                form.on('dms-change', selectors.join(','), loadNextStage);
            } else {
                form.on('dms-change', '.form-group[data-field-name]', loadNextStage);
            }
        });
    });
});