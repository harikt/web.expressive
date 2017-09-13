<?php /** @var \Dms\Web\Expressive\Http\ModuleContext $moduleContext */ ?>
<?php /** @var \Dms\Web\Expressive\Renderer\Widget\WidgetRendererCollection $widgetRenderers */ ?>
<?php /** @var \Dms\Core\Widget\IWidget[] $widgets */ ?>
@foreach($widgets as $widget)
    <div class="row">
        <div class="col-sm-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ $widget->getLabel() }}</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    {!! $widgetRenderers->findRendererFor($moduleContext, $widget)->render($moduleContext, $widget) !!}
                </div>
                <!-- /.box-footer -->
            </div>
        </div>
    </div>
@endforeach