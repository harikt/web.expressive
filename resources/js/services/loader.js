Dms.loader.loaders = {};

Dms.loader.register = function (loaderName, loadCallback, doneCallback) {
    if (typeof (Dms.loader.loaders[loaderName]) === 'undefined') {
        Dms.loader.loaders[loaderName] = {
            loaded: false,
            callbacks: []
        };

        loadCallback(function () {
            Dms.loader.loaders[loaderName].loaded = true;

            $.each(Dms.loader.loaders[loaderName].callbacks, function (index, callback) {
                callback();
            });
        });
    }

    if (Dms.loader.loaders[loaderName].loaded) {
        doneCallback();
    } else {
        Dms.loader.loaders[loaderName].callbacks.push(doneCallback);
    }
};