<div class="dms-wysiwyg-container"
     data-load-file-picker-url="{{ route('dms::package.module.dashboard', ['package' => 'documents', 'module' => 'files']) }}"
>
<textarea
        name="{{ $name }}"
        class="{{ $lightMode ? 'dms-wysiwyg-light' : 'dms-wysiwyg' }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($readonly) readonly @endif

        @if($exactLength !== null)
        minlength="{{ $exactLength }}"
        maxlength="{{ $exactLength }}"
        @else
        @if($minLength !== null) minlength="{{ $minLength }}" @endif
        @if($maxLength !== null) maxlength="{{ $maxLength }}" @endif
        @endif
>{{ $value }}</textarea>

    <div class="modal dms-file-picker-dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times"></i></span>
                    </button>
                    <h4 class="modal-title">Files</h4>
                </div>
                <div class="modal-body">
                    <div class="dms-file-picker-container">
                        <div class="dms-file-picker"></div>
                        @include('dms::partials.spinner')
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left btn-close" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</div>
