Dms.utilities.getCsrfHeaders = function (csrfToken) {
    return {
        'X-CSRF-TOKEN': csrfToken || Dms.config.csrf.token
    };
};

Dms.csrf.initializeCallbacks.push(function (csrfToken) {
    $.ajaxSetup({
        headers: Dms.utilities.getCsrfHeaders(csrfToken)
    });
});

Dms.csrf.initializeCallbacks.push(function (csrfToken) {
    $('form[method=post],form[method=POST] input[name=_token]').val(csrfToken);
});