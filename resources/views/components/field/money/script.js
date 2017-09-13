Dms.form.initializeCallbacks.push(function (element) {
    element.find('.dms-money-input-group').each(function () {
        var inputGroup = $(this);
        var moneyInput = inputGroup.find('.dms-money-input');
        var currencyInput = inputGroup.find('.dms-currency-input');

        moneyInput.attr({
            'type': 'number',
            'data-parsley-type': 'number'
        });

        var updateDecimalDigits = function () {
            var selectedOption = currencyInput.children('option:selected');

            var decimalDigits = selectedOption.attr('data-fractional-digits');
            moneyInput.attr('step', Math.pow(0.1, decimalDigits).toFixed(decimalDigits));
        };

        currencyInput.on('change', updateDecimalDigits);
        updateDecimalDigits();

        var updateShouldSubmitData = function () {
            inputGroup.toggleClass('dms-form-no-submit', moneyInput.val() === '');
        };

        moneyInput.on('change input', updateShouldSubmitData);
        updateShouldSubmitData();
    });
});