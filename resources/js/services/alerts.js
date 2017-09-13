Dms.alerts.add = function (type, title, message, timeout) {
    var alertsList = $('.alerts-list');
    var templates = alertsList.find('.alert-templates');


    var alert = templates.find('.alert.alert-' + type).clone(true);

    if (!message) {
        var typeTitle = type.charAt(0).toUpperCase() + type.slice(1);

        alert.find('.alert-title').text(typeTitle);
        alert.find('.alert-message').text(title);
    } else {
        alert.find('.alert-title').text(title);
        alert.find('.alert-message').text(message);
    }

    alertsList.append(alert.hide());
    alert.fadeIn();

    setTimeout(function () {
        if (alert.is(':visible')) {
            alert.fadeOut();
        }
    }, timeout || 10000);
};

Dms.global.initializeCallbacks.push(function () {
    var flashMessage = Cookies.getJSON('dms-flash-alert');

    if (flashMessage) {
        Cookies.remove('dms-flash-alert');

        Dms.alerts.add(flashMessage.type, flashMessage.message);
    }
});