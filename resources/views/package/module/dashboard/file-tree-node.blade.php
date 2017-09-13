<?php /** @var \Dms\Web\Expressive\Http\ModuleContext $moduleContext */ ?>
<?php /** @var \Dms\Web\Expressive\Document\PublicFileModule $module */ ?>
<?php /** @var string $rootDirectory */ ?>
<?php /** @var \Dms\Web\Expressive\Document\DirectoryTree $directoryTree */ ?>

<ul class="list-group dms-object-list">
    @foreach($directoryTree->subDirectories as $subDirectory)
        <li class="list-group-item dms-folder-item dms-folder-closed"
            data-folder-path="{{ substr($subDirectory->directory->getFullPath(), strlen($rootDirectory)) }}">
            <i class="fa fa-folder"></i>
            <i class="fa fa-folder-open"></i>
            {{ $subDirectory->getName() }}

            @include('dms::package.module.dashboard.file-tree-node', ['directoryTree' => $subDirectory])
        </li>
    @endforeach

    <li class="list-group-item dms-file-list clearfix">
        <div class="row">
            @foreach ($directoryTree->files as $file)
                <?php $fileId = $dataSource->getObjectId($file) ?>
                <?php $isImage = in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'svg', ]) ?>

                <div class="col-sm-4 col-md-3 col-lg-2 dms-file-item{{ $isImage ? ' dms-image-item' : '' }}"
                     data-id="{{ $fileId }}"
                     @if ($isPublic)
                     data-public-url="{{ asset_file_url($file) }}"
                    @endif
                >
                    <div class="panel panel-default">
                        <div class="panel-heading" style="text-overflow: ellipsis; white-space: nowrap;overflow: hidden">
                            <img src="{{ asset('vendor/dms/img/file/icon/' . strtolower($file->getExtension()) . '.png') }}"/>

                            {{ $file->getClientFileNameWithFallback() }}
                        </div>

                        <div class="panel-body">
                            @if ($isPublic && $isImage)
                                <div class="dms-image-preview">
                                    <img src="{{ asset_file_url($file) }}"/>
                                </div>
                            @else
                                <p><strong>Size: </strong> {{ \Dms\Web\Expressive\Util\FileSizeFormatter::formatBytes($file->getSize()) }}</p>
                                <p>
                                    <strong>Created At: </strong>
                                    {{ Dms\Common\Structure\DateTime\DateTime::fromString('@' . $file->getInfo()->getCTime())->format(\Dms\Common\Structure\DateTime\DateTime::DISPLAY_FORMAT) }}
                                </p>
                                <p>
                                    <strong>Modified At: </strong>
                                    {{ Dms\Common\Structure\DateTime\DateTime::fromString('@' . $file->getInfo()->getMTime())->format(\Dms\Common\Structure\DateTime\DateTime::DISPLAY_FORMAT) }}
                                </p>
                            @endif
                        </div>

                        <div class="panel-footer dms-file-action-buttons ">
                            @if($isTrash ?? false)
                                @if ($moduleContext->getModule()->getAction('restore-file')->isAuthorized())
                                <span class="dms-run-action-form inline"
                                      data-action="{{ $moduleContext->getUrl('action.run', ['restore-file', 'file' => $fileId]) }}"
                                      data-after-run-remove-closest="li"
                                      data-reload-page-after-submit="1"
                                      data-method="post">
                                    {!! csrf_field() !!}
                                    <button type="submit" class="btn btn-xs btn-success">
                                        <i class="fa fa-life-ring"></i>
                                    </button>
                                </span>
                                @endif
                            @else
                                @if ($moduleContext->getModule()->getAction('download')->isAuthorized())
                                <span class="dms-run-action-form inline"
                                      data-action="{{ $moduleContext->getUrl('action.run', ['download', 'object' => $fileId]) }}"
                                      data-method="post">
                                    {!! csrf_field() !!}
                                    <button type="submit" class="btn btn-xs btn-success">
                                        <i class="fa fa-download"></i>
                                    </button>
                                </span>
                                @endif
                                @if ($moduleContext->getModule()->getAction('details')->isAuthorized())
                                <a href="{{ $moduleContext->getUrl('action.show', ['action' => 'details', 'object_id' => $fileId]) }}" title="View Details"
                                   class="btn btn-xs btn-info">
                                    <i class="fa fa-bars"></i>
                                </a>
                                @endif
                                @if ($moduleContext->getModule()->getAction('edit')->isAuthorized())
                                <a href="{{ $moduleContext->getUrl('action.form', ['edit', $fileId]) }}" title="Edit"
                                   class="btn btn-xs btn-primary">
                                    <i class="fa fa-pencil-square-o"></i>
                                </a>
                                @endif
                                @if ($moduleContext->getModule()->getAction('remove')->isAuthorized())
                                    <span class="dms-run-action-form inline"
                                          data-action="{{ $moduleContext->getUrl('action.run', ['remove', 'object' => $fileId]) }}"
                                          data-after-run-remove-closest="li"
                                          data-method="post">
                                        {!! csrf_field() !!}
                                        <button type="submit" class="btn btn-xs btn-danger">
                                            <i class="fa fa-trash-o"></i>
                                        </button>
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </li>


    @if ($directoryTree->subDirectories->count() + $directoryTree->files->count() === 0)
        <li class="list-group-item">
            <div class="help-block">This folder is empty</div>
        </li>
    @endif
</ul>
