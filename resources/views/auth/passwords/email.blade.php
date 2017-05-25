@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('auth.pwd_reset_header')</div>
        <div class="panel-body">
            @if(session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            {!! BootForm::open(['route' => 'password.email']) !!}
                {!! BootForm::text('login_or_email', trans('auth.login_or_email')) !!}
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        @lang('auth.btn_pwd_reset_email')
                    </button>
                    <a class="btn btn-link" href="{{ route('login') }}">
                        @lang('ui.cancel')
                    </a>
                </div>
            {!! BootForm::close() !!}
        </div>
    </div>
@endsection
