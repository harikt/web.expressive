@extends('dms::template.auth')
@section('content')
    <p class="login-box-msg">Enter your email to reset your password</p>

    <form action="{{ route('dms::auth.password.forgot') }}" method="post">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">

        <div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
            <input type="email" name="email" class="form-control" placeholder="Email">
            <span class="fa fa-user form-control-feedback"></span>

            @if ($errors->has('email'))
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>
        <div class="row">
            <!-- /.col -->
            <div class="col-xs-12">
                <button type="submit" class="btn btn-primary btn-block btn-flat">Send Reset Link</button>
            </div>
            <!-- /.col -->
        </div>
    </form>
@endsection
