@extends('layouts.popup')

@section('header')
    <div class="pageTitle_wrapper">
        <div class="pageTitle">@lang('auth.login_pwd_header')</div>
        <div class="subtitle">@lang('auth.login_pwd_intro')</div>
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
                {!! BootForm::open(['route' => 'login']) !!}
                    {!! BootForm::text('login', false, null, ['placeholder' => trans('auth.login_or_email'), 'prefix' => BootForm::addonText('Aa')]) !!}
                    {!! BootForm::password('password', false, ['placeholder' => trans('auth.pwd'), 'prefix' => BootForm::addonIcon('key fas')]) !!}
                    {!! BootForm::submit(trans('auth.btn_login'), ['class' => 'btn btn-rounded btn-wide btn-primary']) !!}
                    <div class="checkboxSwitch">
                    {!! BootForm::checkbox('remember', trans('auth.remember_me') . '<span class="bg"><span class="cursor"></span></span>') !!}
                    </div>
                    <hr/>
                    <div class="form-group">
                        <label>@lang('auth.link_reset_pwd_label')</label>

                        <a class="btn-link pull-right" href="{{ route('password.request') }}">
                            @lang('auth.link_reset_pwd_link')
                        </a>
                    </div>
                {!! BootForm::close() !!}
                </div>
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
