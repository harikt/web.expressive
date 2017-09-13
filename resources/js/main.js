window.Dms = {
    config: {
        // @see /resources/views/partials/js-config.blade.php
    },
    global: {
        initialize: function (element) {
            $.each(Dms.global.initializeCallbacks, function (index, callback) {
                callback(element);
            });
        },
        initializeCallbacks: []
    },
    action: {
        responseHandler: null // @see ./services/action-response.js
    },
    alerts: {
        add: null // @see ./services/alerts.js
    },
    csrf: {
        initialize: function (csrfToken) {
            Dms.config.csrf.token = csrfToken;

            $.each(Dms.csrf.initializeCallbacks, function (index, callback) {
                callback(csrfToken);
            });
        },
        initializeCallbacks: []
        // @see ./services/csrf.js
    },
    ajax: {
        interceptors: []
        // @see ./services/ajax.js
    },
    link: {
        // @see ./services/links.js
    },
    auth: {
        // @see ./services/auth.js
    },
    form: {
        initialize: function (element) {
            var callbacks = Dms.form.initializeCallbacks.concat(Dms.form.initializeValidationCallbacks);

            $.each(callbacks, function (index, callback) {
                callback(element);
            });
        },
        stages: {}, // @see ./services/form-stages.js
        validation: {}, // @see ./services/validation/form-validation.js
        initializeCallbacks: [],
        initializeValidationCallbacks: []
    },
    table: {
        initialize: function (element) {
            $.each(Dms.table.initializeCallbacks, function (index, callback) {
                callback(element);
            });
        },
        initializeCallbacks: []
    },
    chart: {
        initialize: function (element) {
            $.each(Dms.chart.initializeCallbacks, function (index, callback) {
                callback(element);
            });
        },
        initializeCallbacks: []
    },
    widget: {
        initialize: function (element) {
            $.each(Dms.widget.initializeCallbacks, function (index, callback) {
                callback(element);
            });
        },
        initializeCallbacks: []
    },
    all: {
        initialize: function (element) {
            Dms.csrf.initialize(Dms.config.csrf.token);
            Dms.global.initialize(element);
            Dms.form.initialize(element);
            Dms.table.initialize(element);
            Dms.chart.initialize(element);
            Dms.widget.initialize(element);
        }
    },
    loader: {}, // @see ./services/loader.js,
    utilities: {}, // @see ./services/utilities.js
    controls: {} // @see ./services/controls/*.js
};

$(document).ready(function () {
    Dms.all.initialize($(document));
});