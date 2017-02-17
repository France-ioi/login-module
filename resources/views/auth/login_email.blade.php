@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('auth.login_header')</div>
        <div class="panel-body">
            {!! BootForm::open(['route' => 'login']) !!}
                {!! BootForm::text('login', trans('auth.login_or_email')) !!}
                {!! BootForm::password('password', trans('auth.pwd')) !!}
                {!! BootForm::checkbox('remember_me', trans('auth.remember_me')) !!}
                {!! BootForm::submit(trans('auth.btn_login')) !!}
                <hr/>
                <div class="form-group">
                    <a class="btn btn-link" href="{{ route('password.request') }}">
                        @lang('auth.link_reset_pwd')
                    </a>
                    <a class="btn btn-link pull-right" href="{{ route('register') }}">
                        @lang('auth.link_register')
                    </a>
                </div>
            {!! BootForm::close() !!}
        </div>
    </div>
@endsection
