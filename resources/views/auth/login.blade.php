@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('auth.login_header', ['platform_name' => $platform_name])</div>
        <div class="panel-body">
            {!! BootForm::open(['route' => 'login']) !!}
                {!! BootForm::text('login', trans('auth.login_or_email')) !!}
                {!! BootForm::password('password', trans('auth.pwd')) !!}
                {!! BootForm::checkbox('remember', trans('auth.remember_me')) !!}
                {!! BootForm::submit(trans('auth.btn_login')) !!}
                <hr/>
                <div class="form-group">
                    <a class="btn btn-link" href="{{ route('password.request') }}">
                        @lang('auth.link_reset_pwd')
                    </a>
                    <a class="btn btn-link pull-right" href="{{ url('/auth') }}">
                        @lang('auth.select_another_method')
                    </a>
                </div>
            {!! BootForm::close() !!}
        </div>
    </div>

    <script type="text/javascript">
    // Auto-focus either login or password field
    $(function() {
        if($('#login').val() && $('#password').is(':visible')) {
            $('#password').focus();
        } else if($('#login').is(':visible')) {
            $('#login').focus();
        }
    });
    </script>
@endsection
