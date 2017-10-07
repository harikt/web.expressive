Dms.form.initializeCallbacks.push(function (element) {
    element.find('.dms-inner-module, .dms-display-inner-module').each(function () {
        var innerModule = $(this);

        if (innerModule.data('dms-has-initialized-form')) {
            return;
        } else {
            innerModule.data('dms-has-initialized-form', true);
        }

        var fieldName = innerModule.attr('data-name');
        var formGroup = innerModule.closest('.form-group');
        var rootUrl = innerModule.attr('data-root-url');
        var isDisplayOnly = innerModule.attr('data-display-only');
        var reloadStateUrl = rootUrl + '/state';
        var innerModuleFormContainer = innerModule.find('.dms-inner-module-form-container');
        var innerModuleForm = innerModuleFormContainer.find('.dms-inner-module-form');
        var formStage = innerModule.closest('.dms-form-stage');
        var stagedForm = innerModule.closest('.dms-staged-form');
        var currentValue = JSON.parse(innerModule.attr('data-value') || '[]');

        if (innerModule.attr('data-readonly')) {
            innerModule.find(':input').attr('readonly', 'readonly');
        }

        var fieldDataPrefix = '__field_action_data';
        var interceptor;

        Dms.ajax.interceptors.push(interceptor = {
            accepts: function (options) {
                return options.url.indexOf(rootUrl) === 0 && options.url !== reloadStateUrl;
            },
            before: function (options) {
                var formData;

                if (isDisplayOnly) {
                    formData = Dms.ajax.createFormData();
                    formData.append('__initial_dependent_data', '1')
                } else {
                    formData = Dms.form.stages.getDependentDataForStage(formStage);
                }


                formData.append(fieldDataPrefix + '[current_state]', JSON.stringify(currentValue));
                formData.append(fieldDataPrefix + '[request][url]', options.url.substring(rootUrl.length));
                formData.append(fieldDataPrefix + '[request][method]', options.__emulatedType || options.type || 'get');

                var parametersPrefix = fieldDataPrefix + '[request][parameters]';
                $.each(Dms.ajax.parseData(options.data), function (name, entries) {
                    $.each(entries, function (index, entry) {
                        formData.append(Dms.utilities.combineFieldNames(parametersPrefix, name), entry.value, entry.filename);
                    });
                });

                options.__originalDataType = options.dataType;
                options.dataType = 'json';
                if ((options.type || 'get').toLowerCase() === 'get') {
                    options.data = formData.toQueryString();
                } else {
                    options.processData = false;
                    options.contentType = false;
                    options.data = formData;
                }
            },
            after: function (options, response, data) {
                if (response.statusText === 'abort') {
                    return;
                }

                if (data) {
                    currentValue = data['new_state'];

                    return Dms.ajax.convertResponse(options.__originalDataType, data.response);
                } else {
                    data = JSON.parse(response.responseText);
                    currentValue = data['new_state'];

                    response.responseText = data.response;
                    console.log(response.responseText);
                }
            }
        });

        var originalResponseHandler = Dms.action.responseHandler;
        Dms.action.responseHandler = function (httpStatusCode, actionUrl, response) {
            if (actionUrl.indexOf(rootUrl) !== 0 || httpStatusCode >= 400) {
                originalResponseHandler(httpStatusCode, actionUrl, response);
                return;
            }

            if (response.redirect) {
                var redirectUrl = response.redirect;
                delete response.redirect;

                if (!Dms.utilities.areUrlsEqual(redirectUrl, rootUrl)) {
                    loadModulePage(redirectUrl);
                }
            }

            originalResponseHandler(httpStatusCode, actionUrl, response);

            innerModule.find('.dms-table-control .dms-table').triggerHandler('dms-load-table-data');
            innerModuleForm.empty();
            formGroup.trigger('dms-change');
        };

        var rootActionUrl = rootUrl + '/action/';
        var currentAjaxRequest;

        var loadModulePage = function (url) {
            innerModuleFormContainer.addClass('loading');
            Dms.utilities.scrollToView(innerModuleFormContainer);

            if (currentAjaxRequest) {
                currentAjaxRequest.abort();
            }

            currentAjaxRequest = Dms.ajax.createRequest({
                url: url,
                type: 'post',
                __emulatedType: 'get',
                dataType: 'html',
                data: {'__content_only': 1}
            });

            currentAjaxRequest.done(function (html) {
                innerModuleForm.html(html);
                innerModuleForm.find('[data-reload-page-after-submit]').removeAttr('data-reload-page-after-submit');
                Dms.form.initialize(innerModuleForm);
            });

            currentAjaxRequest.fail(function (response) {
                if (currentAjaxRequest.statusText === 'abort') {
                    return;
                }

                Dms.controls.showErrorDialog({
                    title: "Could not load form",
                    text: "An unexpected error occurred",
                    type: "error",
                    debugInfo: response.responseText
                });
            });

            currentAjaxRequest.always(function () {
                innerModuleFormContainer.removeClass('loading');
                currentAjaxRequest = null;
            });
        };

        innerModule.on('click', 'a[href^="' + rootActionUrl + '"]', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var link = $(this);

            loadModulePage(link.attr('href'));
        });

        innerModule.closest('.form-group').on('dms-get-input-data', function () {
            var fieldData = {};
            fieldData[fieldName] = currentValue;
            return fieldData;
        });

        innerModule.closest('.form-group').on('dms-set-input-data', function (event, fieldData) {
            var newValue = fieldData[fieldName] || [];

            if (currentValue != newValue) {
                currentValue = newValue;
                innerModule.find('.dms-table').triggerHandler('dms-load-table-data');
            }
        });

        stagedForm.on('dms-before-submit', function () {
            innerModuleForm.empty();
        });

        var hasReset = false;
        var resetAjaxInterception = function () {
            if (hasReset) {
                return;
            } else {
                hasReset = true;
            }

            Dms.ajax.interceptors.splice(Dms.ajax.interceptors.indexOf(interceptor), 1);
            Dms.action.responseHandler = originalResponseHandler;
        };

        formStage.on('dms-stage-reload', resetAjaxInterception);
        stagedForm.on('dms-post-submit-success', resetAjaxInterception);
        innerModule.closest('.dms-page-content').on('dms-page-unloading', resetAjaxInterception);
    });
});