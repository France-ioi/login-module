@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('auth.register_header')</div>
        <div class="panel-body">
            {!! BootForm::open(['route' => 'register']) !!}
                @if($login_required)
                    {!! BootForm::text('login', trans('auth.login')) !!}
                @endif
                @if($email_required)
                    {!! BootForm::text('primary_email', trans('auth.email')) !!}
                @endif
                {!! BootForm::password('password', trans('auth.pwd')) !!}
                {!! BootForm::password('password_confirmation', trans('auth.pwd_confirm')) !!}
                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            @lang('auth.btn_register')
                        </button>
                        <a class="btn btn-link" href="{{ route('login') }}">
                            @lang('auth.link_cancel')
                        </a>
                    </div>
                </div>
            {!! BootForm::close() !!}
        </div>
    </div>
@endsection
