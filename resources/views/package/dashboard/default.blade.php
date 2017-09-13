<?php /** @var \Dms\Web\Expressive\Renderer\Widget\WidgetRendererCollection $widgetRenderers */ ?>
<?php /** @var \Dms\Core\Module\IModule $module */ ?>
<?php /** @var \Dms\Core\Package\IDashboardWidget[] $widgets */ ?>
@foreach($widgets as $widget)
    <?php $moduleContext = app(\Dms\Web\Expressive\Http\ModuleRequestRouter::class)->getRootContextFromModule($widget->getModule()) ?>
    <?php $renderer = $widgetRenderers->findRendererFor($moduleContext, $widget->getWidget()) ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ $widget->getWidget()->getLabel() }}</h3>
                    <div class="pull-right box-tools">
                        @foreach ($renderer->getLinks($moduleContext, $widget->getWidget()) as $url => $label)
                            <a class="btn btn-sm btn-default" href="{{ $url }}">
                                {{ $label }} &nbsp; <i class="fa fa-arrow-right"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    {!! $renderer->render($moduleContext, $widget->getWidget()) !!}
                </div>
                <!-- /.box-footer -->
            </div>
        </div>
    </div>
@endforeach