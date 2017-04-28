@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('auth.pwd_reset_header')</div>

        <div class="panel-body">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            @if($errors->any())
                <ul class="alert alert-danger">
                    @foreach($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            @endif

            {!! BootForm::open(['route' => 'password.request']) !!}
                {!! BootForm::email('email', trans('auth.email'), $email) !!}
                {!! BootForm::text('token', trans('password.token'), $token) !!}
                {!! BootForm::password('password', trans('password.pwd_new')) !!}
                {!! BootForm::password('password_confirmation', trans('auth.pwd_confirm')) !!}
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        @lang('auth.btn_pwd_reset')
                    </button>
                    <a class="btn btn-link" href="{{ route('login') }}">
                        @lang('ui.cancel')
                    </a>
                </div>
            {!! BootForm::close() !!}
        </div>
    </div>
@endsection
