@extends('layouts.popup')

@section('content')
    <div class="pageTitle_wrapper">
        <div class="pageTitle">@lang('auth.pwd_reset_header')</div>
        <div class="subtitle">@lang('auth.pwd_reset_intro')</div>
    </div>
    <div class="panel panel-auth">
        <div class="panel-heading">
            <a class="back_link" href="{{ url('/auth') }}">
                <i class="fa fa-arrow-left"></i>
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
                        {!! BootForm::text('login_or_email', false, null, ['placeholder' => trans('auth.login_or_email')]) !!}
                        {!! BootForm::submit(trans('auth.btn_pwd_reset_email'), ['class' => 'btn btn-rounded btn-wide btn-primary']) !!}
                        <div class="form-group">
                            <a class="btn btn-danger btn-wide btn-rounded" href="{{ route('login') }}">
                                @lang('ui.cancel')
                            </a>
                        </div>
                    {!! BootForm::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
