Dms.form.initializeCallbacks.push(function (element) {

    element.find('ul.dms-field-list').each(function () {
        var listOfFields = $(this);
        var form = listOfFields.closest('.dms-staged-form');
        var formGroup = listOfFields.closest('.form-group');
        var templateField = listOfFields.children('.field-list-template');
        var addButton = listOfFields.children('.field-list-add').find('.btn-add-field');
        var guid = Dms.utilities.idGenerator();
        var isInvalidating = false;

        var minFields = listOfFields.attr('data-min-elements');
        var maxFields = listOfFields.attr('data-max-elements');

        var getAmountOfInputs = function () {
            return listOfFields.children('.field-list-item').length;
        };

        var invalidateControl = function () {
            if (isInvalidating) {
                return;
            }

            isInvalidating = true;

            var amountOfInputs = getAmountOfInputs();

            addButton.prop('disabled', amountOfInputs >= maxFields);
            listOfFields.find('.dms-remove-field-button').prop('disabled', amountOfInputs <= minFields);

            while (amountOfInputs < minFields) {
                addNewField();
                amountOfInputs++;
            }

            isInvalidating = false;
        };

        var reindexFields = function () {
            // TODO
        };

        var addNewField = function () {
            var newField = templateField.clone()
                .removeClass('field-list-template')
                .removeClass('hidden')
                .removeClass('dms-form-no-submit')
                .addClass('field-list-item');

            var fieldInputElement = newField.find('.field-list-input');
            fieldInputElement.html(fieldInputElement.text());

            var currentIndex = getAmountOfInputs();

            $.each(['name', 'data-name', 'data-field-name'], function (index, attr) {
                fieldInputElement.find('[' + attr + '*="::index::"]').each(function () {
                    $(this).attr(attr, $(this).attr(attr).replace('::index::', currentIndex));
                });
            });

            addButton.closest('.field-list-add').before(newField);

            Dms.form.initialize(fieldInputElement);
            form.triggerHandler('dms-form-updated');

            invalidateControl();
        };

        listOfFields.on('click', '.dms-remove-field-button', function () {
            var field = $(this).closest('.field-list-item');
            field.remove();
            formGroup.trigger('dms-change');
            form.triggerHandler('dms-form-updated');

            invalidateControl();
            reindexFields();
        });

        addButton.on('click', addNewField);

        invalidateControl();

        var requiresAnExactAmountOfFields = typeof minFields !== 'undefined' && minFields === maxFields;
        if (requiresAnExactAmountOfFields && getAmountOfInputs() == minFields) {
            addButton.closest('.field-list-add').remove();
            listOfFields.find('.dms-remove-field-button').closest('.field-list-button-container').remove();
            listOfFields.find('.field-list-input').removeClass('col-xs-10 col-md-11').addClass('col-xs-12');
        }

        // Sorting
        var sortable = new Sortable(listOfFields.get(0), {
            group: "sortable-field-list-" + guid,
            sort: true,  // sorting inside list
            animation: 150,  // ms, animation speed moving items when sorting, `0` — without animation
            handle: ".dms-reorder-field-button",  // Drag handle selector within list items
            draggable: ".list-group-item",  // Specifies which items inside the element should be sortable
            ghostClass: "sortable-ghost",  // Class name for the drop placeholder
            chosenClass: "sortable-chosen",  // Class name for the chosen item
            dataIdAttr: 'data-id',
            onEnd: function (event) {
                reindexFields();
                formGroup.trigger('dms-change');
            }
        });

    });
});