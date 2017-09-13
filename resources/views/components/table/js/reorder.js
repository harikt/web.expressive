Dms.table.initializeCallbacks.push(function (element) {
    var groupCounter = 0;

    element.find('.dms-table-body-sortable').each(function () {
        var tableBody = $(this);
        var table = tableBody.closest('.dms-table');
        var control = tableBody.closest('.dms-table-control');
        var reorderRowsUrl = control.attr('data-reorder-row-action-url');

        var performReorder = function (event) {
            var newIndex = typeof event.newIndex === 'undefined' ? event.oldIndex : event.newIndex;

            var criteria = control.data('dms-table-criteria');
            var row = $(event.item);
            var objectId = row.find('.dms-row-action-column').attr('data-object-id');
            var reorderButtonHandle = row.find('.dms-drag-handle');

            var reorderRequest = Dms.ajax.createRequest({
                url: reorderRowsUrl,
                type: 'post',
                dataType: 'html',
                data: {
                    object: objectId,
                    index: criteria.offset + newIndex + 1
                }
            });

            if (reorderButtonHandle.is('button')) {
                reorderButtonHandle.addClass('ladda-button').attr('data-style', 'expand-right');
                var ladda = Ladda.create(reorderButtonHandle.get(0));
                ladda.start();

                reorderRequest.always(ladda.stop);
            }

            reorderRequest.done(function () {
                table.triggerHandler('dms-load-table-data');
            });

            reorderRequest.fail(function (response) {
                Dms.controls.showErrorDialog({
                    title: "Could not reorder item",
                    text: "An unexpected error occurred",
                    type: "error",
                    debugInfo: response.responseText
                });
            });
        };

        var sortable = new Sortable(tableBody.get(0), {
            group: "sortable-group" + groupCounter++,
            sort: true,  // sorting inside list
            animation: 150,  // ms, animation speed moving items when sorting, `0` — without animation
            handle: ".dms-drag-handle",  // Drag handle selector within list items
            draggable: "tr",  // Specifies which items inside the element should be sortable
            ghostClass: "sortable-ghost",  // Class name for the drop placeholder
            chosenClass: "sortable-chosen",  // Class name for the chosen item
            dataIdAttr: 'data-id',

            onEnd: performReorder

        });
    });
});