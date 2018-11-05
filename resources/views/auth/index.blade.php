@extends('layouts.popup')

@push('login_email_code')
    <a class="btn btn-block btn-default" data-toggle="collapse" data-target="#box-login_email_code">
        @lang('auth.login_email_badge')
        <span id="login-caret" class="glyphicon glyphicon-triangle-top"></span>
    </a>
    <div id="box-login_email_code" class="collapse in btn-block">
        <div class="well">
            {!! BootForm::open(['url' => 'login_with_code']) !!}
                {!! BootForm::hidden('try_code', 1) !!}
                {!! BootForm::hidden('try_password', 1) !!}
                {!! BootForm::text('identity', trans('auth.login_email_badge')) !!}
                {!! BootForm::checkbox('remember', trans('auth.remember_me')) !!}
                {!! BootForm::submit(trans('auth.btn_login')) !!}
                <hr/>
                <div class="form-group">
                    <a class="btn btn-link" href="{{ route('register') }}">
                        @lang('auth.link_register')
                    </a>
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
        <div class="well">
            {!! BootForm::open(['url' => 'login_with_code']) !!}
                {!! BootForm::hidden('try_password', 1) !!}
                {!! BootForm::text('identity', trans('auth.login_or_email')) !!}
                {!! BootForm::checkbox('remember', trans('auth.remember_me')) !!}
                {!! BootForm::submit(trans('auth.btn_login')) !!}
                <hr/>
                <div class="form-group">
                    <a class="btn btn-link" href="{{ route('register') }}">
                        @lang('auth.link_register')
                    </a>
                </div>
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
    <a class="btn btn-block btn-default" href="/oauth_client/redirect/google">Google</a>
@endpush

@push('facebook')
    <a class="btn btn-block btn-default" href="/oauth_client/redirect/facebook">Facebook</a>
@endpush

@push('pms')
    <a class="btn btn-block btn-default" href="/oauth_client/redirect/pms">PMS</a>
@endpush



@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('auth.login_header', ['platform_name' => $platform_name])</div>
        <div class="panel-body">
            <p>@lang('auth.login_intro')</p>

            <div class="list-group">
                @foreach($methods['visible'] as $method)
                    @stack($method)
                @endforeach
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
    </script>

@endsection
