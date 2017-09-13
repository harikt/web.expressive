Dms.link.isLocalLink = function (url) {
    var rootUrl = Dms.config.routes.localUrls.root;
    var excludedUrls = Dms.config.routes.localUrls.exclude;

    if (url.indexOf(rootUrl) !== 0) {
        return false;
    }

    var isExcluded = false;

    $.each(excludedUrls, function (index, excludedUrl) {
        if (Dms.utilities.areUrlsEqual(url, excludedUrl)) {
            isExcluded = true;
        }
    });

    return !isExcluded;
};

Dms.link.goToUrl = function (url) {
    if (Dms.link.isLocalLink(url)) {
        $('<a/>').attr({href: url}).hide().appendTo(document.body).click();
    } else {
        window.location.href = url;
    }
};

Dms.link.reloadCurrentPage = function () {
    Dms.link.goToUrl(window.location.href);
};

Dms.global.initializeCallbacks.push(function (element) {
    element.click(function (e) {
        if ($(this).attr('disabled')) {
            e.stopImmediatePropagation();

            return false;
        }

        return true;
    });


    if (Dms.config.hasLoadedAjaxPageNavigation) {
        return;
    } else {
        Dms.config.hasLoadedAjaxPageNavigation = true;
    }

    var rootUrl = Dms.config.routes.localUrls.root;
    var contentContainer = element.find('.content-wrapper');
    var contentElement = contentContainer.children('.dms-page-content');
    var isPoppingState = false;

    if (contentElement.length === 0) {
        return;
    }

    var loadedScripts = $.map($('script[src]').toArray(), function (script) {
        return $(script).attr('src');
    });

    var loadedStyles = $.map($('link[rel=stylesheet][href]').toArray(), function (style) {
        return $(style).attr('href');
    });

    var loadedRequiredAssets = function (page, callback) {
        var scriptToLoad = $.map(page.find('#page > .scripts > script[src]').toArray(), function (script) {
            return $(script).attr('src');
        });

        var styleToLoad = $.map(page.find('#page > .styles > link[rel=stylesheet][href]').toArray(), function (style) {
            return $(style).attr('href');
        });

        $.each(styleToLoad, function (index, css) {
            if ($.inArray(css, loadedStyles) === -1) {
                $('<link/>', {
                    rel: 'stylesheet',
                    type: 'text/css',
                    href: css
                }).appendTo('head');

                loadedStyles.push(css);
            }
        });

        var scriptSemaphore = 0;
        var scriptsTotal = 0;

        $.each(scriptToLoad, function (index, script) {
            if ($.inArray(script, loadedScripts) === -1) {
                scriptsTotal++;

                $.getScript(script, function () {
                    scriptSemaphore++;

                    if (scriptSemaphore === scriptsTotal) {
                        callback();
                    }
                });

                loadedScripts.push(script);
            }
        });

        if (scriptsTotal === 0) {
            callback();
        }
    };

    var currentAjaxRequest;

    element.on('click', 'a[href^="' + rootUrl + '"]:not([download]):not([data-no-ajax])', function (e) {
        var link = $(this);
        var linkUrl = link.attr('href');

        if (!Dms.link.isLocalLink(linkUrl)) {
            return;
        }

        // Ignore hash of current page, not a link just scrolling
        var hashPos = linkUrl.indexOf('#');
        if (hashPos === 0 || (hashPos !== -1 && linkUrl.split('#')[0] === window.location.split('#')[0])) {
            return;
        }

        // Ignore non-left clicks and key combinations that open a new tab
        if (typeof e.which !== 'undefined' && e.which !== 1 || e.ctrlKey || e.altKey || e.shiftKey) {
            return;
        }

        if (e.isDefaultPrevented()) {
            return;
        }

        e.preventDefault();
        e.stopImmediatePropagation();

        if (currentAjaxRequest) {
            currentAjaxRequest.abort();
        }

        contentContainer.addClass('loading');

        currentAjaxRequest = Dms.ajax.createRequest({
            url: linkUrl,
            type: 'get',
            dataType: 'html',
            data: {'__no_template': 1}
        });

        currentAjaxRequest.done(function (content) {
            var page = $('<div>' + content + '</div>');

            var finalUrl = currentAjaxRequest.responseURL || linkUrl;
            currentAjaxRequest = null;

            loadedRequiredAssets(page, function () {
                if (!link.attr('id')) {
                    link.attr('id', Dms.utilities.idGenerator());
                }

                contentElement.triggerHandler('dms-page-unloading');
                contentElement.unbind().empty().append(page.find('#page > .content > *'));
                contentContainer.removeClass('loading');
                Dms.all.initialize(contentElement);

                if (link.closest('.dms-packages-nav').length) {
                    link.closest('li').addClass('active').siblings().removeClass('active');
                }

                document.title = page.find('#page > .title').text();

                if (!isPoppingState) {
                    history.pushState({page: finalUrl, linkId: link.attr('id')}, '', linkUrl);
                } else {
                    isPoppingState = false;
                }
            });
        });

        currentAjaxRequest.fail(function (response) {
            if (currentAjaxRequest.statusText === 'abort') {
                return;
            }

            Dms.controls.showErrorDialog({
                title: "Could not load page",
                text: "An unexpected error occurred",
                type: "error",
                debugInfo: response.responseText
            });

            contentContainer.removeClass('loading');
        });
    });

    $(window).on('popstate', function (e) {
        isPoppingState = true;
        var linkId = e.originalEvent.state.linkId;
        var link = $('#' + linkId);

        if (link.length) {
            link.click();
        } else {
            Dms.link.goToUrl(e.originalEvent.state.page);
        }
    });
});