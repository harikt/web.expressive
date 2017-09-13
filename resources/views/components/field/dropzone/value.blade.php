<div class="dms-file-display">
    <ul class="list-group">
        @foreach($existingFiles as $file)
            <li class="list-group-item clearfix">
                <div class="row">
                    <div class="col-xs-9">
                        @if (!empty($file['width']) && !empty($file['height']))
                            <p>
                                <img src="{{ $file['previewUrl'] }}" alt="{{ $file['name'] }}"/>
                            </p>
                        @endif
                        <p><label>Name:</label> {{ $file['name'] }}</p>
                        <p><label>Size:</label> {{ \Dms\Web\Expressive\Util\FileSizeFormatter::formatBytes($file['size']) }}</p>
                    </div>
                    <div class="col-xs-3">
                        <a href="{{ $file['downloadUrl'] }}" download="{{ $file['name'] }}" class="btn btn-success pull-right">
                            Download <i class="fa fa-download"></i>
                        </a>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</div>