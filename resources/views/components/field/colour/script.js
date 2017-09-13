Dms.form.initializeCallbacks.push(function (element) {
    element.find('input.dms-colour-input').each(function () {
        var config = {
            theme: 'bootstrap'
        };

        if ($(this).hasClass('dms-colour-input-rgb')) {
            config.format = 'rgb';
        } else if ($(this).hasClass('dms-colour-input-rgba')) {
            config.format = 'rgb';
            config.opacity = true;
        }

        $(this).addClass('minicolors').minicolors(config);
    });
});