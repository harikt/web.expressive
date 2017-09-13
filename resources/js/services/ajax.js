Dms.ajax.formData = function (form) {
    var formValues = {};
    var nativeFormData = new FormData(form);

    this.getNativeFormData = function () {
        return nativeFormData;
    };

    this.append = function (name, value, filename) {
        if (typeof formValues[name] === 'undefined') {
            formValues[name] = [];
        }

        formValues[name].push({
            value: value,
            filename: filename
        });

        if (typeof filename !== 'undefined') {
            nativeFormData.append(name, value, filename);
        } else {
            nativeFormData.append(name, value);
        }

        return nativeFormData;
    };

    this.getFormValues = function () {
        return formValues;
    };

    this.toQueryString = function () {
        var params = [];

        $.each(formValues, function (name, entries) {
            $.each(entries, function (index, entry) {
                params.push({name: name, value: entry.value});
            });
        });

        return $.param(params);
    };
};

Dms.ajax.createFormData = function (form) {
    return new Dms.ajax.formData(form);
};

Dms.ajax.convertResponse = function (dataType, response) {
    if (dataType === 'json') {
        return JSON.parse(response);
    } else if (dataType === 'xml') {
        return $.parseXML(dataType);
    }

    return response;
};

Dms.ajax.parseData = function (data) {
    if (typeof data === 'undefined' || data === null) {
        return [];
    }

    if (data instanceof Dms.ajax.formData) {
        return data.getFormValues();
    }

    var dataMap = {};

    var queryString = $.param(data);

    if (queryString === '') {
        return dataMap;
    }

    $.each(queryString.split('&'), function (index, parameter) {
        var parts = parameter.split('=');
        var name = decodeURIComponent(parts[0].replace(/\+/g, '%20'));
        if (typeof dataMap[name] === 'undefined') {
            dataMap[name] = [];
        }

        dataMap[name].push({value: decodeURIComponent(parts[1].replace(/\+/g, '%20'))});
    });

    return dataMap;
};

Dms.ajax.createRequest = function (options) {
    var originalOptions = $.extend(true, {}, options);
    var filteredInterceptors = [];

    $.each(Dms.ajax.interceptors, function (index, interceptor) {
        if (typeof interceptor.accepts !== 'function' || interceptor.accepts(options)) {
            filteredInterceptors.push(interceptor);
        }
    });

    $.each(filteredInterceptors, function (index, interceptor) {
        if (typeof interceptor.before === 'function') {
            interceptor.before(options);
        }
    });

    var optionsAfterBeforeFilters = $.extend(true, {}, options);
    var areHandlersCanceled = false;

    var callAfterInterceptors = function (response, data) {
        $.each(filteredInterceptors.reverse(), function (index, interceptor) {
            if (typeof interceptor.after === 'function') {

                var returnValue = interceptor.after(optionsAfterBeforeFilters, response, data);

                if (typeof returnValue !== 'undefined') {
                    data = returnValue;
                }
            }
        });

        return data;
    };

    var handleLoggedOutDueToSessionExpiry = function (response) {
        if (Dms.auth.isLoggedOut(response)) {
            areHandlersCanceled = true;

            Dms.auth.handleActionWhenLoggedOut(function () {
                var newRequest = Dms.ajax.createRequest(originalOptions);

                $.each(doneCallbacks, function (index, callback) {
                    newRequest.done(callback);
                });

                $.each(failCallbacks, function (index, callback) {
                    newRequest.fail(callback);
                });

                $.each(alwaysCallbacks, function (index, callback) {
                    newRequest.always(callback);
                });
            });
        }
    };

    var responseData;

    var originalErrorCallback = options.error;
    options.error = function (jqXHR, textStatus, errorThrown) {
        handleLoggedOutDueToSessionExpiry(jqXHR);

        if (areHandlersCanceled) {
            return;
        }
        
        callAfterInterceptors(jqXHR);

        if (originalErrorCallback) {
            return originalErrorCallback.apply(this, arguments);
        }
    };

    var originalSuccessCallback = options.success;
    options.success = function (data, textStatus, jqXHR) {
        responseData = data = callAfterInterceptors(jqXHR, data);

        if (areHandlersCanceled) {
            return;
        }

        if (originalSuccessCallback) {
            return originalSuccessCallback.apply(this, [data, textStatus, jqXHR]);
        }
    };

    if (options.data instanceof Dms.ajax.formData) {
        options.data = options.data.getNativeFormData();
    }

    var request = $.ajax(options);

    var doneCallbacks = [];
    var failCallbacks = [];
    var alwaysCallbacks = [];

    var originalDone = request.done;
    request.done = function (callback) {
        if (!callback) {
            return;
        }

        doneCallbacks.push(callback);

        originalDone(function (data, textStatus, jqXHR) {

            if (areHandlersCanceled) {
                return;
            }

            callback(responseData, textStatus, jqXHR);
        });
    };

    var originalFail = request.fail;
    request.fail = function (callback) {
        if (!callback) {
            return;
        }

        originalFail(function () {
            failCallbacks.push(callback);

            if (areHandlersCanceled) {
                return;
            }

            callback.apply(this, arguments);
        });
    };

    var originalAlways = request.always;
    request.always = function (callback) {
        if (!callback) {
            return;
        }

        originalAlways(function () {
            alwaysCallbacks.push(callback);

            if (areHandlersCanceled) {
                return;
            }

            callback.apply(this, arguments);
        });
    };

    return request;
};

