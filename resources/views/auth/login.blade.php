@inject('urlHelper', 'Zend\Expressive\Helper\UrlHelper')

@extends('dms::template.auth')
@section('content')
    <p class="login-box-msg">Log in to continue</p>
    @if ($errors->has('csrf_token'))
        <div class="alert alert-danger">
            {{ $errors->first('csrf_token') }}
        </div>
    @endif
    <form action="{{ $serverUrlHelper->generate($urlHelper->generate('dms::auth.login')) }}" method="post">
        {!! csrf_token() !!}

        <div class="form-group has-feedback{{ $errors->has('username') ? ' has-error' : '' }}">
            <input type="text" name="username" class="form-control" placeholder="Username" value="">
            <span class="fa fa-user form-control-feedback"></span>

            @if ($errors->has('username'))
                <span class="help-block">
                    <strong>{{ $errors->first('username') }}</strong>
                </span>
            @endif
        </div>
        <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
            <input type="password" name="password" class="form-control" placeholder="Password">
            <span class="fa fa-lock form-control-feedback"></span>

            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
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
