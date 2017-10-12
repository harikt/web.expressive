@inject('urlHelper', 'Zend\Expressive\Helper\UrlHelper')

@extends('dms::template.template')
@section('body-content')
    <body class="hold-transition login-page">

    <div class="login-box-logo">
        <div class="login-logo">
            <a href="{{ $urlHelper->generate('dms::index') }}"><b>DMS</b> <br/> </a>
        </div>
    </div>
    <div class="login-box">
        <div class="login-box-body">
            @include('dms::partials.alerts')
            @yield('content')
        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->
    </body>
@endsection
