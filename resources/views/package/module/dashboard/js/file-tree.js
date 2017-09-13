Dms.table.initializeCallbacks.push(function (element) {
    element.find('.dms-file-tree').each(function () {
        var fileTree = $(this);
        var allFileTrees = fileTree.find('.dms-file-tree-data');
        var fileTreeData = fileTree.find('.dms-file-tree-data.dms-file-tree-data-active');
        var trashedFileTreeData = fileTree.find('.dms-file-tree-data.dms-file-tree-data-trash');
        var filterForm = fileTree.find('.dms-quick-filter-form');
        var reloadFileTreeUrl = fileTree.attr('data-reload-file-tree-url');

        var initializeFileTreeData = function (fileTreeData) {
            var folderItems = fileTreeData.find('.dms-folder-item');
            var fileItems = fileTreeData.find('.dms-file-item');

            fileTree.find('.dms-folder-item').on('click', function (e) {
                if ($(e.target).is('.dms-file-item, .dms-file-item *')) {
                    return;
                }

                e.stopImmediatePropagation();
                $(this).toggleClass('dms-folder-closed');
            });

            filterForm.find('input[name=filter]').on('change input', function () {
                var filterBy = $(this).val();

                folderItems.hide().addClass('.dms-folder-closed');
                fileItems.each(function (index, fileItem) {
                    fileItem = $(fileItem);
                    var label = fileItem.text();

                    var doesContainFilter = label.toLowerCase().indexOf(filterBy.toLowerCase()) !== -1;
                    fileItem.toggleClass('hidden', !doesContainFilter || fileItem.hasClass('hidden-file-item'));

                    if (doesContainFilter) {
                        fileItem.parents('.dms-folder-item').removeClass('dms-folder-closed').show();
                    }
                });

                hideEmptyFolders(fileTreeData);
            });

            hideEmptyFolders(fileTreeData);
        };

        var hideEmptyFolders = function (fileTreeData) {
            fileTreeData.find('.dms-folder-item').each(function () {
                $(this).toggle($(this).find('.dms-file-item:not(.hidden)').length > 0);
            });
        };

        element.find('.dms-upload-form .dms-staged-form').on('dms-post-submit-success', function () {
            var fileTreeContainer = fileTree.find('.dms-file-tree-data-container');

            var request = Dms.ajax.createRequest({
                url: reloadFileTreeUrl,
                type: 'get',
                dataType: 'html',
                data: {'__content_only': '1'}
            });

            fileTreeContainer.addClass('loading');

            request.done(function (html) {
                var newFileTrees = $(html).find('.dms-file-tree-data');
                var newActiveFileTree = newFileTrees.filter('.dms-file-tree-data-active');
                fileTreeData.replaceWith(newActiveFileTree);
                var newTrashedFileTree = newFileTrees.filter('.dms-file-tree-data-trashed');
                trashedFileTreeData.replaceWith(newTrashedFileTree);
                initializeFileTreeData(newActiveFileTree.parent());
                initializeFileTreeData(newTrashedFileTree.parent());

                Dms.form.initialize(newActiveFileTree.parent());
                Dms.form.initialize(newTrashedFileTree.parent());

                allFileTrees = newFileTrees;
                fileTreeData = newActiveFileTree;
                trashedFileTreeData = newTrashedFileTree;

                fileTree.triggerHandler('dms-file-tree-updated');
            });

            request.always(function () {
                fileTreeContainer.removeClass('loading');
            });
        });

        fileTree.find('.btn-images-only').on('click', function () {
            allFileTrees.find('.dms-file-item:not(.dms-image-item)').addClass('hidden').addClass('hidden-file-item');
            hideEmptyFolders(allFileTrees);
        });

        fileTree.find('.btn-all-files').on('click', function () {
            allFileTrees.find('.dms-file-item').removeClass('hidden').removeClass('hidden-file-item');
            hideEmptyFolders(allFileTrees);
        });

        fileTree.find('.btn-trashed-files').on('click', function () {
            fileTreeData.toggleClass('hidden');
            trashedFileTreeData.toggleClass('hidden');
        });

        fileTree.find('.btn-group > .btn').click(function(){
            $(this).addClass('active').siblings().removeClass('active');
        });

        allFileTrees.each(function () {
            initializeFileTreeData($(this));
        });
    });
});
