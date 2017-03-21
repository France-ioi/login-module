@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('auth.register_header')</div>
        <div class="panel-body">
            {!! BootForm::open(['route' => 'register']) !!}
                @if($login_required)
                    {!! BootForm::text('login', trans('auth.login'), array_get($values, 'login')) !!}
                @endif
                @if($email_required)
                    {!! BootForm::text('primary_email', trans('auth.email'), array_get($values, 'email')) !!}
                @endif
                {!! BootForm::password('password', trans('auth.pwd')) !!}
                {!! BootForm::password('password_confirmation', trans('auth.pwd_confirm')) !!}
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        @lang('auth.btn_register')
                    </button>
                    <a class="btn btn-link" href="{{ route('login') }}">
                        @lang('ui.cancel')
                    </a>
                </div>
            {!! BootForm::close() !!}
        </div>
    </div>
@endsection
