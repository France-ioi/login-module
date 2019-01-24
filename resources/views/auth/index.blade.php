@extends('layouts.popup')

@push('login_email_code')
    <div id="box-login_email_code" class="btn-block">
        <div class="">
            {!! BootForm::open(['url' => 'login_with_code']) !!}
                {!! BootForm::hidden('try_code', 1) !!}
                {!! BootForm::hidden('try_password', 1) !!}
                {!! BootForm::text('identity', false, null, ['placeholder' => trans('auth.login_email_badge'), 'prefix' => BootForm::addonText('Aa')]) !!}
                {!! BootForm::submit(trans('auth.btn_login'), ['class' => 'btn btn-rounded btn-wide btn-primary']) !!}
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



@section('content')
    <div class="pageTitle_wrapper">
        <div class="pageTitle">@lang('auth.login_choice_header')</div>
        <div class="subtitle">@lang('auth.login_choice_intro')</div>
    </div>
    <div class="panel panel-auth">
        <div class="row">
            <div class="col-sm-6 hasBorder">
                <div class="panelTitle">@lang('auth.login_email_badge')</div>
                @foreach($methods['visible'] as $method)
                    @if ($method == 'login_email_code' )
                            @stack($method)
                    @endif
                @endforeach
            </div>
            <div class="col-sm-6">
                <div class="panelTitle">@lang('auth.login_services')</div>
                @foreach($methods['visible'] as $method)
                    @if ($method == 'login_email_code' )
                        @continue
                    @endif
                        @stack($method)
                @endforeach
            </div>
        </div>

        <div class="highlightBlock">
            <a class="btn btn-link" href="{{ route('register') }}">
                @lang('auth.link_register')
            </a>
        </div>

        @if(count($methods['hidden']))
            <div>
                <hr>
                <button id="btn-show-hidden" class="btn btn-block btn-link" data-toggle="collapse" data-target="#auth-hidden">@lang('auth.show_more')</button>
            </div>
            <div id="auth-hidden" class="collapse">
                @foreach($methods['hidden'] as $method)
                    @stack($method)
                @endforeach
            </div>
        @endif
    </div>

    <script type="text/javascript">
        $('#btn-show-hidden').click(function(e) {
            $(e.target).closest('div').hide();
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
