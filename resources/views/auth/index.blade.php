@extends('layouts.popup')

@push('login_email_code')
    <div id="box-login_email_code" class="btn-block">
        <div class="">
            {!! BootForm::open(['url' => 'login_with_code']) !!}
                {!! BootForm::hidden('try_code', 1) !!}
                {!! BootForm::hidden('try_password', 1) !!}
                {!! BootForm::text('identity', false, null, ['placeholder' => trans('auth.login_email_badge'), 'prefix' => BootForm::addonText('Aa')]) !!}
                <!--
                {!! BootForm::submit('<i class="fa fa-chevron-down"></i>'.trans('auth.btn_login'), ['class' => 'btn btn-rounded btn-wide btn-primary']) !!}
                -->
                <button type="submit" class="btn btn-rounded btn-wide btn-primary">
                    <i class="fa fa-chevron-down icon"></i>
                    @lang('ui.continue')
                </button>
                <div class="checkboxSwitch">
                {!! BootForm::checkbox('remember', trans('auth.remember_me') . '<span class="bg"><span class="cursor"></span></span>') !!}
                </div>
            {!! BootForm::close() !!}
        </div>
    </div>
@endpush

@push('login_email')
    <a class="btn btn-block btn-default" data-toggle="collapse" data-target="#box-login_email">
        @lang('auth.login_or_email')
        <span id="login-caret" class="glyphicon glyphicon-triangle-top"></span>
    </a>
    <div id="box-login_email" class="collapse in btn-block">
        <div class="">
            {!! BootForm::open(['url' => 'login_with_code']) !!}
                {!! BootForm::hidden('try_password', 1) !!}
                {!! BootForm::text('identity', false, null, ['placeholder' => trans('auth.login_or_email'), 'prefix' => BootForm::addonText('Aa')]) !!}
                {!! BootForm::submit(trans('auth.btn_login')) !!}
                {!! BootForm::checkbox('remember', trans('auth.remember_me')) !!}
            {!! BootForm::close() !!}
        </div>
    </div>
@endpush

@push('code')
    <a class="btn btn-block btn-default" data-toggle="collapse" data-target="#box-code">
        @lang('badge.header')
        <span id="login-caret" class="glyphicon glyphicon-triangle-top"></span>
    </a>
    <div id="box-code" class="collapse in btn-block">
        <div class="well">
            {!! BootForm::open(['url' => 'login_with_code']) !!}
                {!! BootForm::hidden('try_code', 1) !!}
                {!! BootForm::text('identity', trans('badge.header')) !!}
                {!! BootForm::checkbox('remember', trans('auth.remember_me')) !!}
                {!! BootForm::submit(trans('auth.btn_login')) !!}
            {!! BootForm::close() !!}
        </div>
    </div>
@endpush

@push('google')
    <div class="form-group">
        <a class="btn btn-primary btn-wide btn-rounded" href="/oauth_client/redirect/google"><i class="fab fa-google icon"></i>Google</a>
    </div>
@endpush

@push('facebook')
    <div class="form-group">
        <a class="btn btn-primary btn-wide btn-rounded" href="/oauth_client/redirect/facebook"><i class="fab fa-facebook-f icon"></i>Facebook</a>
    </div>
@endpush

@push('pms')
    <div class="form-group">
        <a class="btn btn-primary btn-wide btn-rounded" href="/oauth_client/redirect/pms">PMS</a>
    </div>
@endpush

@section('header')
    <div class="pageTitle_wrapper">
        <div class="pageTitle">@lang('auth.login_choice_header')</div>
        <div class="subtitle">@lang('auth.login_choice_intro')</div>
    </div>
@endsection

@section('content')
    <div class="row">
        @if($left_panel_visible)
            <div class="{{ $right_panel_visible ? 'col-sm-6 hasBorder' : 'col-sm-10 col-sm-offset-1' }} pb-20">
                <div class="panelTitle">@lang('auth.login_email_badge')</div>
                @foreach($methods['visible'] as $method)
                    @if ($method == 'login_email_code' )
                            @stack($method)
                    @endif
                @endforeach
            </div>
        @endif
        @if($right_panel_visible)
            <div class="{{ $left_panel_visible ? 'col-sm-6' : 'col-sm-10 col-sm-offset-1' }} pb-20">
                <div class="panelTitle">@lang('auth.login_services')</div>
                @foreach($methods['visible'] as $method)
                    @if ($method == 'login_email_code' )
                        @continue
                    @endif
                        @stack($method)
                @endforeach

                @if(count($methods['hidden']))
                    <div id="box-show-hidden">
                        <hr>
                        <div id="btn-show-hidden">
                            @lang('auth.show_more')
                            <i class="fa fa-2x fa-angle-down"></i>
                        </div>
                    </div>
                    <div id="auth-hidden" class="collapse">
                        @foreach($methods['hidden'] as $method)
                            @stack($method)
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>

    <div class="highlightBlock">
        <i class="fas fa-user-plus"></i>
        @lang('auth.not_member')
        <a class="btn-link" href="{{ route('register') }}">
            @lang('auth.link_register')
        </a>
        <br>
        <i class="fas fa-user-lock"></i>
        @lang('auth.link_reset_pwd_label')
        <a class="btn-link" href="{{ route('password.request') }}">
            @lang('auth.link_reset_pwd_link')
        </a>
    </div>


    <script type="text/javascript">
        $('#btn-show-hidden').click(function(e) {
            $('#box-show-hidden').hide();
            $('#auth-hidden').collapse('show');
        });
        $('#login-form').on('show.bs.collapse', function() {
            $('#login-caret').removeClass('glyphicon-triangle-bottom').addClass('glyphicon-triangle-top');
        });
        $('#login-form').on('hide.bs.collapse', function() {
            $('#login-caret').removeClass('glyphicon-triangle-top').addClass('glyphicon-triangle-bottom');
        });
        $(function() {
            // Auto-focus identity field if visible
            if($('#identity').is(':visible')) {
                $('#identity').focus();
            }
        });
    </script>

@endsection
