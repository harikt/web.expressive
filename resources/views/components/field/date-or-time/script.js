Dms.form.initializeCallbacks.push(function (element) {
    var convertFromUtcToLocal = function (dateFormat, value) {
        if (value) {
            return moment.utc(value, dateFormat).local().format(dateFormat);
        } else {
            return '';
        }
    };

    var convertFromLocalToUtc = function (dateFormat, value) {
        if (value) {
            return moment(value, dateFormat).utc().format(dateFormat);
        } else {
            return '';
        }
    };

    var submitUtcDateTimeViaHiddenInput = function (stagedForm, dateFormat, originalInput) {
        var inputName = originalInput.data('dms-input-name') || originalInput.attr('name');
        originalInput.removeAttr('name');
        originalInput.data('dms-input-name', inputName);

        stagedForm.find('input[type=hidden][name="' + inputName + '"]').remove();
        stagedForm.append($('<input type="hidden" />').attr('name', inputName).val(convertFromLocalToUtc(dateFormat, originalInput.val())));
    };

    element.find('input.dms-date-or-time').each(function () {
        var inputElement = $(this);
        var formGroup = inputElement.closest('.form-group');
        var stagedForm = formGroup.closest('.dms-staged-form');
        var phpDateFormat = inputElement.attr('data-date-format');
        var dateFormat = Dms.utilities.convertPhpDateFormatToMomentFormat(phpDateFormat);
        var mode = inputElement.attr('data-mode');

        var config = {
            locale: {
                format: dateFormat
            },
            parentEl: inputElement.closest('.dms-date-picker-container'),
            singleDatePicker: true,
            showDropdowns: true,
            autoApply: true,
            linkedCalendars: false,
            autoUpdateInput: false
        };

        if (mode === 'date-time') {
            config.timePicker = true;
            config.timePickerSeconds = phpDateFormat.indexOf('s') !== -1;

            inputElement.val(convertFromUtcToLocal(dateFormat, inputElement.val()));
            stagedForm.on('dms-before-submit', function () {
                submitUtcDateTimeViaHiddenInput(stagedForm, dateFormat, inputElement);
            });
        }

        if (mode === 'time') {
            config.timePicker = true;
            config.timePickerSeconds = phpDateFormat.indexOf('s') !== -1;
        }
        // TODO: timezoned-date-time

        inputElement.daterangepicker(config, function (date) {
            inputElement.val(date.format(dateFormat));
        });

        var picker = inputElement.data('daterangepicker');

        if (inputElement.val()) {
            picker.setStartDate(inputElement.val());
        }

        if (mode === 'time') {
            inputElement.closest('.dms-date-picker-container').find('.calendar-table').hide();
        }

        inputElement.on('apply.daterangepicker', function () {
            formGroup.trigger('dms-change');
        });
    });

    element.find('.dms-date-or-time-range').each(function () {
        var rangeElement = $(this);
        var formGroup = rangeElement.closest('.form-group');
        var stagedForm = formGroup.closest('.dms-staged-form');
        var startInput = rangeElement.find('.dms-start-input');
        var endInput = rangeElement.find('.dms-end-input');
        var claerButton = rangeElement.find('.dms-btn-clear-input');
        var phpDateFormat = startInput.attr('data-date-format');
        var dateFormat = Dms.utilities.convertPhpDateFormatToMomentFormat(phpDateFormat);
        var mode = rangeElement.attr('data-mode');

        var config = {
            locale: {
                format: dateFormat
            },
            parentEl: rangeElement.parent(),
            showDropdowns: true,
            autoApply: !rangeElement.attr('data-dont-auto-apply'),
            linkedCalendars: false,
            autoUpdateInput: false
        };

        if (mode === 'date-time') {
            config.timePicker = true;
            config.timePickerSeconds = phpDateFormat.indexOf('s') !== -1;

            startInput.val(convertFromUtcToLocal(dateFormat, startInput.val()));
            endInput.val(convertFromUtcToLocal(dateFormat, endInput.val()));
            stagedForm.on('dms-before-submit', function () {
                submitUtcDateTimeViaHiddenInput(stagedForm, dateFormat, startInput);
                submitUtcDateTimeViaHiddenInput(stagedForm, dateFormat, endInput);
            });
        }

        if (mode === 'time') {
            config.timePicker = true;
            config.timePickerSeconds = phpDateFormat.indexOf('s') !== -1;
        }
        // TODO: timezoned-date-time

        startInput.daterangepicker(config, function (start, end, label) {
            if (mode === 'date-time') {
                start = start.local();
                end = end.local();
            }

            startInput.val(start.format(dateFormat));
            endInput.val(end.format(dateFormat));
            rangeElement.triggerHandler('dms-range-updated');
        });

        var picker = startInput.data('daterangepicker');

        if (startInput.val()) {
            picker.setStartDate(startInput.val());
        }
        if (endInput.val()) {
            picker.setEndDate(endInput.val());
        }

        endInput.on('focus click', function () {
            startInput.focus();
        });

        if (mode === 'time') {
            rangeElement.parent().find('.calendar-table').hide();
        }

        startInput.on('apply.daterangepicker', function () {
            formGroup.trigger('dms-change');
        });

        claerButton.on('click', function () {
            startInput.val('');
            endInput.val('');
        });

        stagedForm.on('dms-before-submit', function () {
            formGroup.toggleClass('dms-form-no-submit', !startInput.val() && !endInput.val());
        });
    });

    $('.dms-date-or-time-display[data-mode="date-time"]').each(function () {
        var dateTimeDisplay = $(this);
        var dateFormat = Dms.utilities.convertPhpDateFormatToMomentFormat(dateTimeDisplay.attr('data-date-format'));

        dateTimeDisplay.text(convertFromUtcToLocal(dateFormat, dateTimeDisplay.text()));
    });

    $('.dms-date-or-time-range-display[data-mode="date-time"]').each(function () {
        var dateTimeDisplay = $(this);
        var startDisplay = dateTimeDisplay.find('.dms-start-display');
        var endDisplay = dateTimeDisplay.find('.dms-end-display');
        var dateFormat = Dms.utilities.convertPhpDateFormatToMomentFormat(dateTimeDisplay.attr('data-date-format'));

        startDisplay.text(convertFromUtcToLocal(dateFormat, startDisplay.text()));
        endDisplay.text(convertFromUtcToLocal(dateFormat, endDisplay.text()));
    });
});