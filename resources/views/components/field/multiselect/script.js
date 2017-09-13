Dms.form.initializeCallbacks.push(function (element) {
    element.find('select[multiple]').multiselect({
        enableFiltering: true,
        includeSelectAllOption: true
    });
});