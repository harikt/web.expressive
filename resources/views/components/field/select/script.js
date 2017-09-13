Dms.form.initializeCallbacks.push(function (element) {
    element.find('.dms-select-with-remote-data').each(function () {
        var control = $(this);
        var formStage = control.closest('.dms-form-stage')
        var input = control.find('.dms-select-input');
        var hiddenInput = control.find('.dms-select-hidden-input');
        var formGroup = control.closest('.form-group');

        var remoteDataUrl = control.attr('data-remote-options-url');
        var remoteMinChars = control.attr('data-remote-min-chars');

        var currentRequest = null;

        input.typeahead(null, {
            displayKey: 'label',
            hint: true,
            highlight: true,
            minLength: remoteMinChars,
            source: Dms.utilities.debounceCallback(function (query, callback) {
                if (currentRequest) {
                    currentRequest.abort();
                }

                var formData = Dms.form.stages.getDependentDataForStage(formStage);

                currentRequest = Dms.ajax.createRequest({
                    url: remoteDataUrl + '?query=' + encodeURIComponent(query),
                    type: 'POST',
                    dataType: 'json',
                    cache: false,
                    processData: false,
                    contentType: false,
                    data: formData
                });

                currentRequest.done(function (results) {
                    callback(results);
                });
            }, 500)
        }).on('typeahead:selected', function (event, data) {
            hiddenInput.val(data.val);
            formGroup.trigger('dms-change');
        });
    });
});