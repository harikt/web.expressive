Dms.global.initializeCallbacks.push(function (element) {
    var navigationFilter = element.find('.dms-nav-quick-filter');
    var packagesNavigation = element.find('.dms-packages-nav');
    var navigationSections = packagesNavigation.find('li.treeview');
    var navigationLabels = packagesNavigation.find('.dms-nav-label');

    navigationFilter.on('input', function () {
        var filterBy = $(this).val();

        navigationSections.hide();
        var sectionsToShow = [];
        navigationLabels.each(function (index, navItem) {
            navItem = $(navItem);
            var label = navItem.text();

            var doesContainFilter = label.toLowerCase().indexOf(filterBy.toLowerCase()) !== -1;
            navItem.closest('li').toggle(doesContainFilter);

            if (doesContainFilter) {
                navItem.closest('ul.treeview-menu').toggle(doesContainFilter).addClass('menu-open');
                navItem.parents('li.treeview').show();

                if (navItem.is('.dms-nav-label-group')) {
                    sectionsToShow.push(navItem.closest('li.treeview').get(0));
                }
            }
        });

        $(sectionsToShow).find('li').show();
        $(sectionsToShow).find('ul.treeview-menu').show().addClass('menu-open');
    });

    navigationFilter.on('keyup', function (event) {
        var enterKey = 13;

        if (event.keyCode === enterKey) {
            var link = packagesNavigation.find('a[href!="javascript:void(0)"]:visible').first();
            link.click();
            navigationFilter.val('');
            navigationFilter.triggerHandler('input');
        }
    });

    packagesNavigation.on('dms-update-active', function () {
        var currentUrl = window.location.href;

        var currentLink
    });

    element.find('.navbar-static-top .user-menu a[href]').on('click', function () {
        $(this).closest('.user-menu').removeClass('open');
    });
});