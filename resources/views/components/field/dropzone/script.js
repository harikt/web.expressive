Dms.form.initializeCallbacks.push(function (element) {

    element.find('.dropzone-container').each(function () {
        var container = $(this);
        var uniqueId = Dms.utilities.idGenerator();
        var formGroup = container.closest('.form-group');
        var form = container.closest('.dms-staged-form');
        var dropzoneElement = container.find('.dms-dropzone');
        var fieldName = container.attr('data-name');
        var required = container.attr('data-required');
        var tempFilePrefix = container.attr('data-temp-file-key-prefix');
        var uploadTempFileUrl = container.attr('data-upload-temp-file-url');
        var maxFileSize = container.attr('data-max-size');
        var maxFiles = container.attr('max-files');
        var existingFiles = JSON.parse(container.attr('data-files') || '[]');
        var isMultiple = container.attr('data-multi-upload');

        var maxImageWidth = container.attr('data-max-width');
        var minImageWidth = container.attr('data-min-width');
        var maxImageHeight = container.attr('data-max-height');
        var minImageHeight = container.attr('data-min-height');
        var imageEditor = container.find('.dms-image-editor-dialog');

        var getDownloadUrlForFile = function (file) {
            if (file.downloadUrl) {
                return file.downloadUrl;
            }

            if (file.tempFileToken) {
                return container.attr('data-download-temp-file-url').replace('__token__', file.tempFileToken);
            }

            return null;
        };

        var editedImagesQueue = [];
        var isEditingImage = false;

        var showImageEditor = function (file, saveCallback, alwaysCallback, options) {

            if (isEditingImage) {
                editedImagesQueue.push(arguments);
                return;
            }

            isEditingImage = true;
            if (!options) {
                options = {};
            }

            imageEditor.find('.modal-title').text(options.title || 'Edit Image');

            var canvasContainer = imageEditor.find('.dms-canvas-container');

            var imageSrc = getDownloadUrlForFile(file);

            var loadDarkroom = function (imageSrc) {
                var imageElement = $('<img />').attr('src', imageSrc);
                canvasContainer.append(imageElement);

                var darkroom = new Darkroom(imageElement.get(0), $.extend({}, {
                    plugins: {
                        save: false // disable plugin
                    },

                    initialize: function () {
                        imageEditor.appendTo('body').modal('show');
                    }
                }, options));

                imageEditor.find('.btn-save-changes').on('click', function () {
                    var blob = window.dataURLtoBlob(darkroom.canvas.toDataURL());

                    imageEditor.modal('hide');

                    blob.name = file.name;
                    saveCallback(blob);
                    alwaysCallback();
                });

                imageEditor.on('hide.bs.modal', function () {
                    canvasContainer.empty();
                    alwaysCallback();

                    imageEditor.unbind('hide.bs.modal');
                    imageEditor.find('.btn-save-changes').unbind('click');
                    imageEditor.appendTo(container);

                    isEditingImage = false;

                    if (editedImagesQueue.length > 0) {
                        showImageEditor.apply(null, editedImagesQueue.pop());
                    }
                });
            };

            if (imageSrc) {
                loadDarkroom(imageSrc);
            } else {
                var reader = new FileReader();

                reader.addEventListener("load", function () {
                    loadDarkroom(reader.result);
                }, false);

                reader.readAsDataURL(file);
            }
        };

        var acceptedFiles = JSON.parse(container.attr('data-allowed-extensions') || '[]').map(function (extension) {
            return '.' + extension;
        });

        if (container.attr('data-images-only')) {
            acceptedFiles.push('image/*')
        }

        dropzoneElement.attr('id', 'dropzone-' + uniqueId);
        var dropzone = new Dropzone('#dropzone-' + uniqueId, {
            url: uploadTempFileUrl,
            paramName: 'file',
            maxFilesize: maxFileSize,
            maxFiles: isMultiple ? maxFiles : 1,
            acceptedFiles: acceptedFiles.join(','),

            init: function () {
                var dropzone = this;

                this.on("addedfile", function (file) {
                    var removeButton = Dropzone.createElement(
                        '<button type="button" class="btn btn-sm btn-danger btn-remove-file"><i class="fa fa-times"></i></button>'
                    );

                    removeButton.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();

                        dropzone.removeFile(file);

                        if (file.action === 'keep-existing') {
                            file.action = 'delete-existing';
                        }

                        if (dropzone.options.maxFiles === 0) {
                            dropzone.options.maxFiles++;
                        }
                    });

                    file.previewElement.appendChild(removeButton);
                });

                this.on("removedfile", function (file) {
                    if (file.action === 'keep-existing') {
                        file.action = 'delete-existing';
                    }

                    formGroup.trigger('dms-change');
                });

                this.on("complete", function (file) {
                    var downloadButton = Dropzone.createElement(
                        '<button type="button" class="btn btn-sm btn-success btn-download-file"><i class="fa fa-download"></i></button>'
                    );

                    downloadButton.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();

                        Dms.utilities.downloadFileFromUrl(getDownloadUrlForFile(file));
                    });

                    file.previewElement.appendChild(downloadButton);

                    if (file.width && file.height) {
                        var editImageButton = Dropzone.createElement(
                            '<button type="button" class="btn btn-sm btn-info btn-edit-file"><i class="fa fa-pencil-square-o"></i></button>'
                        );

                        editImageButton.addEventListener('click', function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            $(editImageButton).prop('disabled', true);

                            showImageEditor(file, function (newFile) {
                                dropzone.removeFile(file);

                                if (dropzone.options.maxFiles === 0) {
                                    dropzone.options.maxFiles++;
                                }

                                dropzone.addFile(newFile);
                            }, function () {
                                $(editImageButton).prop('disabled', false);
                            });
                        });

                        file.previewElement.appendChild(editImageButton);
                    }

                    formGroup.trigger('dms-change');
                });

                this.on('success', function (file, response) {
                    file.action = 'store-new';
                    file.tempFileToken = response.tokens['file'];
                });

                this.on("thumbnail", function (file) {
                    if (!file.acceptDimensions && !file.rejectDimensions) {
                        return;
                    }

                    if ((maxImageWidth && file.width > maxImageWidth) || (maxImageHeight && file.height > maxImageHeight)
                        || (minImageWidth && file.width < minImageWidth) || (minImageHeight && file.height < minImageHeight)) {
                        file.rejectDimensions();
                    }
                    else {
                        file.acceptDimensions();
                    }
                });

                $.each(existingFiles, function (index, existingFile) {
                    existingFile.originalIndex = index;
                    existingFile.action = 'keep-existing';
                    existingFile.tempFileToken = null;

                    dropzone.emit("addedfile", existingFile);
                    dropzone.createThumbnailFromUrl(existingFile, existingFile.previewUrl);
                    dropzone.emit("complete", existingFile);

                    if (dropzone.options.maxFiles > 0) {
                        dropzone.options.maxFiles--;
                    }
                });

            },

            accept: function (file, done) {
                if (file.type.indexOf('image') === -1) {
                    done();
                }

                file.acceptDimensions = done;
                file.rejectDimensions = function () {
                    showImageEditor(file, function (editedFile) {
                        dropzone.addFile(editedFile);
                    }, function () {
                        try {
                            dropzone.removeFile(file);
                        } catch (e) {
                        }
                    }, {
                        title: 'The supplied image does not match the required dimensions so it has been resized to: (' + formatRequiredDimensions(file) + ')',
                        minWidth: minImageWidth,
                        minHeight: minImageHeight,
                        maxWidth: maxImageWidth,
                        maxHeight: maxImageHeight
                    })
                };
            }
        });

        dropzone.on('sending', function (file, xhr, formData) {
            $.each(Dms.utilities.getCsrfHeaders(), function (name, value) {
                xhr.setRequestHeader(name, value);
            });
        });

        var formatRequiredDimensions = function (file) {
            var min = '', max = '';

            if (minImageWidth && minImageHeight) {
                min = 'min: ' + minImageWidth + 'x' + minImageHeight + 'px';
            }
            else if (minImageWidth) {
                min = 'min width: ' + minImageWidth + 'px';
            }
            else if (minImageHeight) {
                min = 'min height: ' + minImageHeight + 'px';
            }

            if (maxImageWidth && maxImageHeight) {
                max = 'max: ' + maxImageWidth + 'x' + maxImageHeight + 'px';
            }
            else if (maxImageWidth) {
                max = 'max width: ' + maxImageWidth + 'px';
            }
            else if (minImageHeight) {
                max = 'max height: ' + minImageHeight + 'px';
            }

            return (min + ' ' + max).trim();
        };

        dropzoneElement.addClass('dropzone');

        formGroup.on('dms-get-input-data', function () {
            var fieldData = {};
            
            var allFiles = [];

            $.each(existingFiles.concat(dropzone.getAcceptedFiles()), function (index, file) {
                if (file.action === 'delete-existing') {
                    return;
                }

                if (typeof file.originalIndex !== 'undefined') {
                    allFiles[file.originalIndex] = file;
                    return;
                }

                while (typeof allFiles[index] !== 'undefined') {
                    index++;
                }

                allFiles[index] = file;
            });

            $.each(allFiles, function (index, file) {
                if (!file) {
                    return;
                }

                var fileFieldName;
                fileFieldName = isMultiple
                    ? fieldName + '[' + index + ']'
                    : fieldName;

                fieldData[fileFieldName + '[action]'] = file.action;

                if (file.tempFileToken) {
                    fieldData[Dms.utilities.combineFieldNames(tempFilePrefix, fileFieldName + '[file]')] = file.tempFileToken;
                }
            });

            return fieldData;
        });

        dropzoneElement.closest('.dms-staged-form').on('dms-post-submit-success', function () {
            dropzone.destroy();
        });
    });
});