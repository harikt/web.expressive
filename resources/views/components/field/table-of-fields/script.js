Dms.form.initializeCallbacks.push(function (element) {

    element.find('table.dms-field-table').each(function () {
        var tableOfFields = $(this);
        var form = tableOfFields.closest('.dms-staged-form');
        var formGroup = tableOfFields.closest('.form-group');

        var columnFieldTemplate = tableOfFields.find('.field-column-template');
        var rowFieldTemplate = tableOfFields.find('.field-row-template');
        var cellFieldTemplate = tableOfFields.find('.field-cell-template');
        var removeRowTemplate = tableOfFields.find('.remove-row-template');
        var removeColumnTemplate = tableOfFields.find('.remove-column-template');

        var addColumnButton = tableOfFields.find('.btn-add-column');
        var addRowButton = tableOfFields.find('.btn-add-row');

        var hasPredefinedColumns = tableOfFields.attr('data-has-predefined-columns');
        var hasPredefinedRows = tableOfFields.attr('data-has-predefined-rows');
        var hasRowField = tableOfFields.attr('data-has-row-field');

        var isInvalidating = false;

        var minColumns = tableOfFields.attr('data-min-columns') || 1;
        var maxColumns = tableOfFields.attr('data-max-columns');

        var minRows = tableOfFields.attr('data-min-rows');
        var maxRows = tableOfFields.attr('data-max-rows');

        var getAmountOfColumns = function () {
            return tableOfFields.find('thead .table-column').length;
        };

        var getAmountOfRows = function () {
            return tableOfFields.find('tbody .table-row').length;
        };

        var invalidateControl = function () {
            if (isInvalidating) {
                return;
            }

            isInvalidating = true;

            var amountOfColumns = getAmountOfColumns();
            var amountOfRows = getAmountOfRows();

            addColumnButton.prop('disabled', amountOfColumns >= maxColumns);
            tableOfFields.find('.btn-remove-column').prop('disabled', amountOfColumns <= minColumns);

            while (amountOfColumns < minColumns) {
                addNewColumn();
                amountOfColumns++;
            }

            addRowButton.prop('disabled', amountOfRows >= maxRows);
            tableOfFields.find('.btn-remove-row').prop('disabled', amountOfRows <= minRows);

            while (amountOfRows < minRows) {
                addNewRow();
                amountOfRows++;
            }

            isInvalidating = false;
        };

        var createNewCell = function (columnIndex, rowIndex) {
            var newCell = cellFieldTemplate.clone().removeClass('field-cell-template');

            newCell.html(newCell.text());

            $.each(['name', 'data-name', 'data-field-name'], function (index, attr) {
                newCell.find('[' + attr + '*="::column::"]').each(function () {
                    $(this).attr(attr, $(this).attr(attr).replace('::column::', columnIndex));
                });

                newCell.find('[' + attr + '*="::row::"]').each(function () {
                    $(this).attr(attr, $(this).attr(attr).replace('::row::', rowIndex));
                });
            });

            return newCell;
        };

        var addNewColumn = function () {
            var newColumnHeader = columnFieldTemplate.clone().removeClass('field-column-template');

            var fieldContent = newColumnHeader.find('.field-content');
            fieldContent.html(fieldContent.text());

            var currentRow = 0;
            var currentColumn = getAmountOfColumns();

            $.each(['name', 'data-name', 'data-field-name'], function (index, attr) {
                newColumnHeader.find('[' + attr + '*="::column::"]').each(function () {
                    $(this).attr(attr, $(this).attr(attr).replace('::column::', currentColumn));
                });
            });

            var elementsToInit = $(newColumnHeader);

            addColumnButton.closest('.add-column').before(newColumnHeader);

            tableOfFields.find('tr.table-row').each(function (index, row) {
                var newCell = createNewCell(currentColumn, currentRow);

                $(row).find('.add-column').before(newCell);
                elementsToInit.add(newCell);

                currentRow++;
            });

            tableOfFields.find('.add-row .add-column').before(removeColumnTemplate.clone().removeClass('remove-column-button'));

            Dms.form.initialize(elementsToInit);

            form.triggerHandler('dms-form-updated');

            invalidateControl();
        };

        var addNewRow = function () {
            var currentRow = getAmountOfRows();
            var currentColumn = 0;
            var newRow = $('<tr/>').addClass('table-row');

            if (hasRowField) {
                var newRowHeader = rowFieldTemplate.clone().removeClass('field-row-template');

                var fieldContent = newRowHeader.find('.field-content');
                fieldContent.html(fieldContent.text());

                $.each(['name', 'data-name', 'data-field-name'], function (index, attr) {
                    newRowHeader.find('[' + attr + '*="::row::"]').each(function () {
                        $(this).attr(attr, $(this).attr(attr).replace('::row::', currentRow));
                    });
                });

                newRow.append(newRowHeader);
            }

            var amountOfColumns = getAmountOfColumns();
            for (currentColumn = 0; currentColumn < amountOfColumns; currentColumn++) {
                newRow.append(createNewCell(currentColumn, currentRow));
            }

            newRow.append(removeRowTemplate.clone().removeClass('remove-row-template'));

            tableOfFields.find('tr.add-row').before(newRow);

            Dms.form.initialize(newRow);

            form.triggerHandler('dms-form-updated');

            invalidateControl();
        };

        tableOfFields.on('click', '.btn-remove-column', function () {
            var parentCell = $(this).closest('td, th');
            var columnIndex = parentCell.prevAll('td, th').length;
            tableOfFields.find('tr').each(function () {
                $(this).find('td:not(.add-column), th:not(.add-column)').eq(columnIndex).remove();
            });
            parentCell.remove();

            formGroup.trigger('dms-change');
            form.triggerHandler('dms-form-updated');

            invalidateControl();
            // TODO: reindex
        });

        tableOfFields.on('click', '.btn-remove-row', function () {
            $(this).closest('tr').remove();

            formGroup.trigger('dms-change');
            form.triggerHandler('dms-form-updated');

            invalidateControl();
            // TODO: reindex
        });

        addColumnButton.on('click', addNewColumn);
        addRowButton.on('click', addNewRow);

        invalidateControl();

        var requiresAnExactAmountOfColumns = typeof minColumns !== 'undefined' && minColumns === maxColumns;
        var requiresAnExactAmountOfRows = typeof minRows !== 'undefined' && minRows === maxRows;

        if (hasPredefinedColumns || (requiresAnExactAmountOfColumns && getAmountOfColumns() == minColumns)) {
            addColumnButton.remove();
            tableOfFields.find('.btn-remove-column').remove();
            tableOfFields.find('.btn-add-column').remove();
        }

        if (hasPredefinedRows || (requiresAnExactAmountOfRows && getAmountOfRows() == minRows)) {
            addRowButton.remove();
            tableOfFields.find('.btn-remove-row').remove();
            tableOfFields.find('.btn-add-row').remove();
        }
    });
});