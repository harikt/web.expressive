<?php /** @var \Dms\Web\Expressive\Renderer\Action\ActionButton[] $actionButtons */ ?>
@extends('dms::template.default')

@section('content')
    <div class="row dms-action-form-content">
        <div class="col-sm-12">
            <div class="box">
                @if($actionButtons || $objectLabel)
                    <div class="box-header with-border clearfix">
                        <h3 class="box-title">{{ $objectLabel }}</h3>
                        <div class="pull-right box-tools">
                            @include('dms::package.module.action-buttons', ['actionButtons' => $actionButtons])
                        </div>
                    </div>
                @endif
                <!-- /.box-header -->
                <div class="box-body">
                    {!! $actionFormContent !!}
                </div>
                <!-- /.box-footer -->
            </div>
        </div>
    </div>
@endsection
