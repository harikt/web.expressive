Dms.form.initializeCallbacks.push(function (element) {
    element.find('input[type="ip-address"]')
        .attr('type', 'text')
        .attr('data-parsley-ip-address', '1');

    element.find('input[data-autocomplete]').each(function () {
        var options = JSON.parse($(this).attr('data-autocomplete'));
        $(this).removeAttr('data-autocomplete');

        var values = [];

        $.each(options, function (index, value) {
            values.push({ val: value });
        });

        var engine = new Bloodhound({
            local: values,
            datumTokenizer: function(d) {
                return Bloodhound.tokenizers.whitespace(d.val);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace
        });

        engine.initialize();

        $(this).typeahead( {
            limit: 5,
            hint: true,
            highlight: true,
            minLength: 1
        }, {
            source: engine.ttAdapter(),
            displayKey: 'val'
        });
    });
});