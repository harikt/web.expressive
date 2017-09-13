window.Parsley.addValidator('ipAddress', {
    requirementType: 'boolean',
    validateString: function (value) {
        var ipV4Regex = /^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.|$)){4}$/;
        var ipV6Regex = /^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))$/;

        if (ipV4Regex.test(value)) {
            return true;
        }

        if (ipV6Regex.test(value)) {
            return true;
        }

        return false;
    },
    messages: {
        en: 'This value should be a valid ip address'
    }
});

window.Parsley.addValidator('maxDecimalPoints', {
    requirementType: 'integer',
    validateString: function (value, requirement) {
        return Dms.utilities.countDecimals(value) <= requirement;
    },
    messages: {
        en: 'This value should have a maximum of %d decimal places'
    }
});

window.Parsley.addValidator('minElements', {
    requirementType: 'integer',
    validateMultiple: function (value, requirement) {
        return value.length >= requirement;
    },
    messages: {
        en: 'At least %s options must be selected'
    }
});

window.Parsley.addValidator('maxElements', {
    requirementType: 'integer',
    validateMultiple: function (value, requirement) {
        return value.length <= requirement;
    },
    messages: {
        en: 'No more than %s options can be selected'
    }
});

