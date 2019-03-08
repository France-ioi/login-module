@extends('layouts.popup')
@section('header')
    <div class="pageTitle_wrapper">
        <div class="pageTitle">@lang('auth.pwd_reset_header')</div>
        <div class="subtitle">@lang('auth.pwd_reset_intro')</div>
    </div>
@endsection
@section('content')
        <div class="panel-heading">
            <a class="back_link" href="{{ url('/auth') }}">
                <i class="fas fa-arrow-left"></i>
                @lang('auth.select_another_method')
            </a>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6 col-centered">
                    <div class="panelTitle">@lang('auth.pwd_reset_title')</div>
                    @if(session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    {!! BootForm::open(['route' => 'password.email']) !!}
                        {!! BootForm::text('login_or_email', false, null, ['placeholder' => trans('auth.login_or_email'), 'prefix' => BootForm::addonText('Aa')]) !!}
                        <div class="form-group">
                            <button type="submit" class="btn btn-rounded btn-wide btn-primary"><i class="fas fa-check icon">    </i>@lang('auth.btn_pwd_reset_email')
                            </button>
                        </div>
                        <div class="form-group">
                            <a class="btn btn-danger btn-wide btn-rounded" href="{{ route('login') }}">
                                <i class="fas fa-times icon"></i>
                                @lang('ui.cancel')
                            </a>
                        </div>
                    {!! BootForm::close() !!}
                </div>
            </div>
        </div>
@endsection
