@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('auth.login_header')</div>
        <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ route('login') }}">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="login" class="col-md-4 control-label">
                        @lang('auth.login_or_email')
                    </label>

                    <div class="col-md-6">
                        <input id="login" type="text" class="form-control" name="login" value="{{ old('login') }}" required autofocus>

                        @if ($errors->has('login'))
                            <span class="help-block">
                                <strong>{{ $errors->first('login') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password" class="col-md-4 control-label">
                        @lang('auth.pwd')
                    </label>

                    <div class="col-md-6">
                        <input id="password" type="password" class="form-control" name="password" required>

                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                @lang('auth.remember_me')
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            @lang('auth.btn_login')
                        </button>

                        <a class="btn btn-link" href="{{ route('password.request') }}">
                            @lang('auth.link_reset_pwd')
                        </a>
                    </div>
                </div>
            </form>

            <hr/>
            <div class="form-group">
                <div class="col-md-8 col-md-offset-4">
                    <a href="{{ route('register') }}">@lang('auth.link_register')</a>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Or sign up with</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-4">
                    <a class="btn btn-block btn-default" href="/oauth_client/redirect/facebook">Facebook</a>
                </div>
                <div class="col-xs-4">
                    <a class="btn btn-block btn-default" href="/oauth_client/redirect/google">Google</a>
                </div>
                <div class="col-xs-4">
                    <a class="btn btn-block btn-default" href="/oauth_client/redirect/pms">PMS</a>
                </div>
            </div>
        </div>
    </div>
@endsection
