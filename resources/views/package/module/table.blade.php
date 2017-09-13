@extends('dms::template.default')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    {!! $tableContent !!}
                </div>
                <!-- /.box-footer -->
            </div>
        </div>
    </div>
@endsection