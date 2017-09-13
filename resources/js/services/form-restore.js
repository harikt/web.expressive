var getAbsoluteName = function (allElements, element) {
    var name = element.name;

    if (name.substr(-2) === '[]') {
        var inputsWithSameNameBefore = allElements
            .filter(function (index, otherElement) {
                return otherElement.name === name;
            })
            .filter(function (index, otherElement) {
                var preceding = 4;
                return otherElement.compareDocumentPosition(element) & preceding;
            });

        name = name.substr(0, name.length - 2) + '[' + inputsWithSameNameBefore.length + ']';
    }

    return name;
};

Dms.form.initializeCallbacks.push(function (element) {
    element.find('form').each(function () {
        var form = $(this);

        var allInputs = form.find(':input');
        form.on('dms-form-updated', function () {
            allInputs = form.find(':input');
        });

        var changedInputs = {};
        form.data('dms-changed-inputs', changedInputs);

        form.on('change input', '*[name]:input', function () {
            changedInputs[getAbsoluteName(allInputs, this)] = true;
        })
    });
});

Dms.global.initializeCallbacks.push(function () {

    $.fn.getValues = function (onlyChanged) {
        var $els = this.find(':input');
        var els = $els.get();
        var changedInputs = $(this).closest('form, .dms-staged-form').data('dms-changed-inputs') || {};

        var data = {};

        $.each(els, function () {
            if (this.name && !this.disabled && (this.checked
                || /select|textarea/i.test(this.nodeName)
                || /text|hidden|password/i.test(this.type))) {
                var absoluteName = getAbsoluteName($els, this);

                if (onlyChanged && !changedInputs[absoluteName]) {
                    return;
                }

                data[absoluteName] = $(this).val();
            }
        });

        return data;
    };

    $.fn.restoreValues = function (data) {
        var $els = this.find(':input');
        var els = $els.get();

        $.each(els, function () {
            if (!this.name) {
                return;
            }

            var name = getAbsoluteName($els, this);

            if (data[name]) {
                var value = data[name];
                var $this = $(this);

                if (this.type == 'checkbox' || this.type == 'radio') {
                    $this.attr("checked", value === $.val());
                } else {
                    $this.val(value);
                }
            }
        });

        return this;
    };
});