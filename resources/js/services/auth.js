Dms.auth.isLoggedOut = function (response) {
    return response.status === 401 && response.responseText.toLowerCase() === 'unauthenticated';
};

Dms.auth.handleActionWhenLoggedOut = function (loggedInCallback) {
    var loginFormUrl = Dms.config.routes.loginUrl;
    var loginDialog = $('.dms-login-dialog');
    var loginDialogContainer = loginDialog.parent();
    var loginFormContainer = loginDialog.find('.dms-login-form-container');
    var loginForm = loginFormContainer.find('.dms-login-form');

    loginDialog.appendTo('body').modal('show');
    loginDialog.on('hidden.bs.modal', function () {
        loginDialog.appendTo(loginDialogContainer);
    });

    var request = Dms.ajax.createRequest({
        url: loginFormUrl,
        type: 'get',
        dataType: 'html',
        data: {'__content_only': '1'}
    });

    loginFormContainer.addClass('loading');

    request.done(function (html) {
        loginForm.html(html);

        loginForm.find('form').on('submit', function (e) {
            var form = $(this);
            e.preventDefault();

            var request = Dms.ajax.createRequest({
                type: 'POST',
                url: Dms.config.routes.loginUrl,
                dataType: 'json',
                data: form.serialize()
            });

            request.done(function (response) {
                loginDialog.modal('hide');
                Dms.csrf.initialize(response.csrf_token);
                loggedInCallback();
            });

            request.fail(function () {
                Dms.alerts.add('danger', 'Login Failed', 'Please verify your login credentials', 5000);
            });
        });
    });

    request.always(function () {
        loginFormContainer.removeClass('loading');
    });

    loginDialog.on('hide.bs.modal', function () {
        loginForm.empty();
    });
};