@inject('urlHelper', 'Zend\Expressive\Helper\UrlHelper')

@extends('dms::template.auth')
@section('content')
    <p class="login-box-msg">Log in to continue</p>
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <form action="" method="post">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">

        <div class="form-group has-feedback}}">
            <input type="text" name="username" class="form-control" placeholder="Username" value="">
            <span class="fa fa-user form-control-feedback"></span>

                <span class="help-block">
                    <strong></strong>
                </span>
        </div>
        <div class="form-group has-feedback }}">
            <input type="password" name="password" class="form-control" placeholder="Password">
            <span class="fa fa-lock form-control-feedback"></span>


                <span class="help-block">
                    <strong></strong>
                </span>

        </div>
        <div class="row">
            <!-- /.col -->
            <div class="col-xs-12">
                <button type="submit" class="btn btn-primary btn-block btn-flat">Log In</button>
            </div>
            <!-- /.col -->
        </div>
    </form>

    <br>
    <a class="btn-block" href="{{ $serverUrlHelper->generate($urlHelper->generate('dms::auth.password.forgot')) }}">I forgot my password</a>
    <br>

    @foreach ($oauthProviders as $oauthProvider)
        <div class="row">
            <div class="col-sm-12">
                <a class="btn btn-block btn-default" data-no-ajax="1" href="{{ $urlHelper->generate('dms::auth.oauth.redirect', ['provider' => $oauthProvider->getName()]) }}">
                    {{ $oauthProvider->getLabel() }}
                </a>
            </div>
        </div>
    @endforeach
@endsection
