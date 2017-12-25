@inject('urlHelper', 'Zend\Expressive\Helper\UrlHelper')

<div class="dropzone-container"
     data-name="{{ $name }}"
     data-field-validation-for="{{ $name }}[action], {{ $name }}[file]"
     @if ($required) data-required="1" @endif
     data-upload-temp-file-url="{{ $serverUrlHelper->generate($urlHelper->generate('dms::file.upload')) }}"
     data-download-temp-file-url="{{ $urlHelper->generate('dms::file.download', ['token' => '__token__']) }}"
     @if($maxFileSize ?? false) data-max-size="{{ $maxFileSize }}" @endif

     @if($multiUpload ?? false) data-multi-upload="1" @endif
     @if($minFiles ?? false) data-min-files="{{ $minFiles }}" @endif
     @if($maxFiles ?? false) data-max-files="{{ $maxFiles }}" @endif
     @if($exactFiles ?? false) data-min-files="{{ $exactFiles }}" data-max-files="{{ $exactFiles }}" @endif

     @if($imagesOnly ?? false) data-images-only="1" @endif
     @if($minImageWidth ?? false) data-min-width="{{ $minImageWidth }}" @endif
     @if($maxImageWidth ?? false) data-max-width="{{ $maxImageWidth }}" @endif
     @if($minImageHeight ?? false) data-min-height="{{ $minImageHeight }}" @endif
     @if($maxImageHeight ?? false) data-max-height="{{ $maxImageHeight }}" @endif

     data-files="{{ json_encode($existingFiles) }}"
     data-temp-file-key-prefix="{{ \Dms\Web\Expressive\Action\InputTransformer\TempUploadedFileToUploadedFileTransformer::TEMP_FILES_KEY }}"
>
    <div class="dms-dropzone">
        <div class="dz-message">
            Drop files here or click to upload. <h3><i class="fa fa-cloud-upload"></i></h3>
        </div>
    </div>

    <div class="modal dms-image-editor-dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times"></i></span>
                    </button>
                    <h4 class="modal-title">Edit Image</h4>
                </div>
                <div class="modal-body">
                    <p class="dms-canvas-container"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left btn-close" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-save-changes">Save changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="darkroom-image-editor"></div>
</div>
