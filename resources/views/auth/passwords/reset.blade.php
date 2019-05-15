@extends('layouts.popup')
@section('header')
    <div class="pageTitle_wrapper">
        <div class="pageTitle">@lang('auth.pwd_reset_header')</div>
        <div class="subtitle">@lang('auth.pwd_reset_intro')</div>
    </div>
@endsection
@section('content')
        <div class="panel-heading">
            <a class="back_link" href="{{ url('auth') }}">
                <i class="fas fa-arrow-left"></i>
                @lang('auth.select_another_method')
            </a>
        </div>

        <div class="panel-body">
            <div class="alert-section">
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
            </div>
            <div class="row">
                <div class="col-sm-6 col-centered">
                {!! BootForm::open(['route' => 'password.request']) !!}
                    {!! BootForm::email('email', false, $email,
                        ['placeholder' => trans('auth.email'),'prefix' => BootForm::addonText('Aa')]) !!}
                    {!! BootForm::text('token', false, $token,
                        ['placeholder' => trans('password.token'),'prefix' => BootForm::addonText('Aa')]) !!}
                    {!! BootForm::password('password', false,
                        ['placeholder' => trans('password.pwd_new'), 'prefix' => BootForm::addonIcon('key fas')]) !!}
                    {!! BootForm::password('password_confirmation', false,
                        ['placeholder' =>trans('auth.pwd_confirm'), 'prefix' => BootForm::addonIcon('key fas')]) !!}
                    <div class="form-group">
                        <button type="submit" class="btn btn-rounded btn-wide btn-primary">
                            <i class="fas fa-check icon"></i>
                            @lang('auth.btn_pwd_reset')
                        </button>
                    </div>
                    <div class="form-group">
                        <a class="btn btn-rounded btn-wide btn-danger" href="{{ route('login') }}">
                            <i class="fas fa-times icon"></i>
                            @lang('ui.cancel')
                        </a>
                    </div>
                {!! BootForm::close() !!}
                </div>
            </div>
        </div>
@endsection
