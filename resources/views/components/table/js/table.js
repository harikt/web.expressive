Dms.table.initializeCallbacks.push(function (element) {

    element.find('.dms-table-control').each(function () {
        var control = $(this);
        var tableContainer = control.find('.dms-table-container');
        var table = tableContainer.find('table.dms-table');
        var filterForm = control.find('.dms-table-quick-filter-form');
        var rowsPerPageSelect = control.find('.dms-table-rows-per-page-form select');
        var paginationPreviousButton = control.find('.dms-table-pagination .dms-pagination-previous');
        var paginationNextButton = control.find('.dms-table-pagination .dms-pagination-next');
        var loadRowsUrl = control.attr('data-load-rows-url');
        var stringFilterableComponentIds = JSON.parse(control.attr('data-string-filterable-component-ids')) || [];

        var currentPage = 0;

        var criteria = {
            orderings: [],
            condition_mode: 'or',
            conditions: [],
            offset: 0,
            max_rows: rowsPerPageSelect.val()
        };

        var currentAjaxRequest;

        var loadCurrentPage = function () {
            if (currentAjaxRequest) {
                currentAjaxRequest.abort();
            }

            tableContainer.addClass('loading');

            criteria.offset = currentPage * criteria.max_rows;

            currentAjaxRequest = Dms.ajax.createRequest({
                url: loadRowsUrl,
                type: 'post',
                dataType: 'html',
                data: criteria
            });

            currentAjaxRequest.done(function (tableData) {
                table.empty().append($(tableData).children());
                Dms.table.initialize(table);
                Dms.form.initialize(table);

                control.data('dms-table-criteria', criteria);
                control.attr('data-has-loaded-table-data', true);

                if (table.find('tbody tr').length < criteria.max_rows) {
                    paginationNextButton.addClass('disabled');
                }

                renderOrderState();
            });

            currentAjaxRequest.fail(function (response) {
                if (currentAjaxRequest.statusText === 'abort') {
                    return;
                }

                tableContainer.addClass('has-error');

                Dms.controls.showErrorDialog({
                    title: "Could not load table data",
                    text: "An unexpected error occurred",
                    type: "error",
                    debugInfo: response.responseText
                });
            });

            currentAjaxRequest.always(function () {
                tableContainer.removeClass('loading');
            });
        };

        var renderOrderState = function () {
            var orderByComponent = criteria.orderings.length ? criteria.orderings[0].component : null;
            var orderDirection = criteria.orderings.length ? criteria.orderings[0].direction : null;

            filterForm.find('[name=component]').val(orderByComponent || '');
            filterForm.find('[name=direction]').val(orderDirection || 'asc');

            table.find('th[data-order]').removeClass('dms-ordered-asc').removeClass('dms-ordered-desc');
            table.find('th[data-order="' + orderByComponent + '"]').addClass('dms-ordered-' + orderDirection);
        };

        filterForm.find('[name=component], [name=direction]').on('change', function () {
            var orderByComponent = filterForm.find('[name=component]').val();

            if (orderByComponent) {
                criteria.orderings = [
                    {
                        component: orderByComponent,
                        direction: filterForm.find('[name=direction]').val()
                    }
                ];
            } else {
                criteria.orderings = [];
            }

            renderOrderState();
            loadCurrentPage();
        });

        table.on('click', 'th[data-order]', function () {
            criteria.orderings = [
                {
                    component: $(this).attr('data-order'),
                    direction: $(this).hasClass('dms-ordered-asc') ? 'desc' : 'asc'
                }
            ];

            renderOrderState();
            loadCurrentPage();
        });

        filterForm.find('button').click(function () {

            criteria.conditions = [];

            var filterByString = filterForm.find('[name=filter]').val();

            if (filterByString) {
                $.each(filterByString.split(' '), function (stringIndex, filterByPart) {
                    $.each(stringFilterableComponentIds, function (index, componentId) {
                        criteria.conditions.push({
                            component: componentId,
                            operator: 'string-contains-case-insensitive',
                            value: filterByString
                        });
                    });
                });
            }

            loadCurrentPage();
        });

        filterForm.find('input[name=filter]').on('keyup', function (event) {
            var enterKey = 13;

            if (event.keyCode === enterKey) {
                filterForm.find('button').click();
            }
        });

        rowsPerPageSelect.on('change', function () {
            criteria.max_rows = $(this).val();

            loadCurrentPage();
        });

        paginationPreviousButton.click(function () {
            currentPage--;
            paginationNextButton.removeClass('disabled');
            paginationPreviousButton.toggleClass('disabled', currentPage === 0);
            loadCurrentPage();
        });

        paginationNextButton.click(function () {
            currentPage++;
            paginationPreviousButton.removeClass('disabled');
            loadCurrentPage();
        });

        paginationPreviousButton.addClass('disabled');

        if (table.is(':visible')) {
            loadCurrentPage();
        }

        table.on('dms-load-table-data', loadCurrentPage);
    });

    $('.dms-table-tabs').each(function () {
        var tabs = $(this);

        tabs.find('.dms-table-tab-show-button').on('click', function () {
            var linkedTablePane = $($(this).attr('href'));

            linkedTablePane.find('.dms-table-control:not([data-has-loaded-table-data]) .dms-table-container:not(.loading) .dms-table').triggerHandler('dms-load-table-data');
        });
    });
});