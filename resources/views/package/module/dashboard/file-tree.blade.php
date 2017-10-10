<?php /** @var \Dms\Web\Expressive\Http\ModuleContext $moduleContext */ ?>
<?php /** @var \Dms\Web\Expressive\Renderer\Table\TableRenderer $tableRenderer */ ?>
<?php /** @var \Dms\Core\Common\Crud\Table\ISummaryTable $summaryTable */ ?>
<?php /** @var string $rootDirectory */ ?>
<?php /** @var \Dms\Web\Expressive\Document\DirectoryTree $directoryTree */ ?>
<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-default dms-file-tree"
             data-reload-file-tree-url="{{ $moduleContext->getUrl('dashboard', ['package' => $moduleContext->getModule()->getPackageName(), 'module' => $moduleContext->getModule()->getName()]) }}"
        >
            <div class="panel-body dms-upload-form">
                {!! app(\Dms\Web\Expressive\Renderer\Form\ActionFormRenderer::class)->renderActionForm($moduleContext, $moduleContext->getModule()->getAction('upload-files')) !!}
            </div>

            <div class="dms-file-tree-header clearfix">
                <div class="col-xs-6">
                    <h3>Files</h3>
                </div>
                <div class="col-xs-6">
                    <span class="pull-right dms-trashed-files-btn-container">
                        &nbsp;
                        <button type="button" class="btn btn-default btn-active-toggle btn-trashed-files"><i class="fa fa-trash-o"></i> Trash</button>
                    </span>
                    <div class="btn-group pull-right">
                        <button type="button" class="btn btn-default btn-images-only"><i class="fa fa-file-image-o"></i> Images</button>
                        <button type="button" class="btn btn-default btn-all-files active"><i class="fa fa-file-text-o"></i> All</button>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="dms-quick-filter-form">
                    <div class="input-group pull-right">
                        <span class="input-group-addon">Filter</span>

                        <div class="form-group">
                            <input name="filter" class="form-control" type="text" placeholder="Filter by name..."/>
                        </div>

                        <span class="input-group-btn">
                            <button class="btn btn-info" type="button"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </div>
            </div>

            <div class="dms-file-tree-data-container">
                <div class="dms-file-tree-data dms-file-tree-data-active">
                    @include('dms::package.module.dashboard.file-tree-node', [
                        'isPublic'      => $isPublic,
                        'moduleContext' => $moduleContext,
                        'module'        => $moduleContext->getModule(),
                        'dataSource'    => $moduleContext->getModule()->getDataSource(),
                        'directoryTree' => $directoryTree,
                    ])
                </div>
                <div class="dms-file-tree-data dms-file-tree-data-trash hidden clearfix">
                    @include('dms::package.module.dashboard.file-tree-node', [
                        'isPublic'      => false,
                        'isTrash'       => true,
                        'moduleContext' => $moduleContext,
                        'module'        => $moduleContext->getModule(),
                        'dataSource'    => $trashDataSource,
                        'directoryTree' => $trashDirectoryTree,
                    ])

                    <div class="col-xs-12 text-center dms-file-tree-empty">
                        <span class="dms-run-action-form inline"
                              data-action="{{ $moduleContext->getUrl('action.run', ['action' => 'empty-trash', 'package' => $moduleContext->getModule()->getPackageName(), 'module' => $moduleContext->getModule()->getName()]) }}"
                              data-reload-page-after-submit="1"
                              data-method="post">
                            <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                            <button type="submit" class="btn btn-danger">
                                <i class="fa fa-trash"></i> Empty Trash
                            </button>
                        </span>
                    </div>
                </div>

                @include('dms::partials.spinner')
            </div>
        </div>
    </div>
</div>
<!-- /.col -->
