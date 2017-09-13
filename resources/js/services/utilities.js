Dms.utilities.countDecimals = function (value) {
    if (value % 1 != 0) {
        return value.toString().split(".")[1].length;
    }

    return 0;
};

Dms.utilities.idGenerator = function () {
    var S4 = function () {
        return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
    };
    return 'id' + (S4() + S4() + "-" + S4() + "-" + S4() + "-" + S4() + "-" + S4() + S4() + S4());
};

Dms.utilities.combineFieldNames = function (outer, inner) {
    if (inner.indexOf('[') === -1) {
        return outer + '[' + inner + ']';
    }

    var firstInner = inner.substring(0, inner.indexOf('['));
    var afterFirstInner = inner.substring(inner.indexOf('['));

    return outer + '[' + firstInner + ']' + afterFirstInner;
};

Dms.utilities.areUrlsEqual = function (first, second) {
    return first.replace(/\/+$/, '') === second.replace(/\/+$/, '');
};

Dms.utilities.downloadFileFromUrl = function (url) {
    downloadFile(url);
};

Dms.utilities.isTouchDevice = function () {
    try {
        document.createEvent("TouchEvent");
        return true;
    } catch (e) {
        return false;
    }
};

Dms.utilities.convertPhpDateFormatToMomentFormat = function (format) {
    var replacements = {
        'd': 'DD',
        'D': 'ddd',
        'j': 'D',
        'l': 'dddd',
        'N': 'E',
        'S': 'o',
        'w': 'e',
        'z': 'DDD',
        'W': 'W',
        'F': 'MMMM',
        'm': 'MM',
        'M': 'MMM',
        'n': 'M',
        'o': 'YYYY',
        'Y': 'YYYY',
        'y': 'YY',
        'a': 'a',
        'A': 'A',
        'g': 'h',
        'G': 'H',
        'h': 'hh',
        'H': 'HH',
        'i': 'mm',
        's': 'ss',
        'u': 'SSS',
        'e': 'zz', // TODO: full timezone id
        'O': 'ZZ',
        'P': 'Z',
        'T': 'zz',
        'U': 'X'
    };

    var newFormat = '';

    $.each(format.split(''), function (index, char) {
        if (replacements[char]) {
            newFormat += replacements[char];
        } else {
            newFormat += char;
        }
    });

    return newFormat;
};

Dms.utilities.isInView = function (element) {
    var topOfElement = element.offset().top;
    if (!element.is(':visible')) {
        element.css({'visibility': 'hidden'}).show();
        topOfElement = element.offset().top;
        element.css({'visibility': '', 'display': ''});
    }
    var bottomOfElement = topOfElement + element.outerHeight();

    var topOfScreen = $(window).scrollTop();
    var bottomOfScreen = topOfScreen + window.innerHeight;

    return topOfScreen < topOfElement && bottomOfScreen > bottomOfElement;
};

Dms.utilities.scrollToView = function (element) {
    if (!Dms.utilities.isInView(element)) {
        // Not in view so scroll to it
        var topOfElement = element.offset().top;
        $('html,body').animate({scrollTop: topOfElement - window.innerHeight / 3}, 500);
    }
};

Dms.utilities.throttleCallback = function (fn, threshhold, scope) {
    var last, deferTimer;

    return function () {
        var context = scope || this;

        var now = +new Date,
            args = arguments;
        if (last && now < last + threshhold) {
            // hold on to it
            clearTimeout(deferTimer);
            deferTimer = setTimeout(function () {
                last = now;
                fn.apply(context, args);
            }, threshhold);
        } else {
            last = now;
            fn.apply(context, args);
        }
    };
};

Dms.utilities.debounceCallback = function (func, wait, immediate) {
    var timeout;
    return function () {
        var context = this, args = arguments;
        var later = function () {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
};