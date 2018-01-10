@extends('layouts.popup')

@push('login')
    <a class="btn btn-block btn-default" href="{{ url('/login_email') }}">@lang('auth.login_password')</a>
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

@push('badge')
    <a class="btn btn-block btn-default" data-toggle="collapse" data-target="#badge-form">@lang('badge.header') <span id="badge-caret" class="glyphicon glyphicon-triangle-bottom"></span></a>
    <div id="badge-form" class="collapse well btn-block">
    {!! BootForm::horizontal(['url' => '/badge/verify']) !!}
        {!! BootForm::text('code', trans('badge.header')) !!}
        {!! BootForm::submit(trans('badge.btn_verify_code')) !!}
    {!! BootForm::close() !!}
    </div>
@endpush

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('auth.login_header')</div>
        <div class="panel-body">
            @if($errors->any())
                <ul class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <li>{{$error}}</li>
                    @endforeach
                </ul>
            @endif
            <p>@lang('auth.login_intro')</p>
            <div class="list-group">
            @foreach($auth_visible as $method)
                @stack($method)
            @endforeach
            </div>

            @if(count($auth_hidden) > 0)
                <hr>
                <button id="btn-show-hidden" class="btn btn-block btn-link" data-toggle="collapse" data-target="#auth-hidden">@lang('auth.show_more')</button>
                <div id="auth-hidden" class="collapse">
                    @foreach($auth_hidden as $method)
                        @stack($method)
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script type="text/javascript">
        $('#btn-show-hidden').click(function(e) {
            $(e.target).hide();
            $('#auth-hidden').show();
        });
        $('#badge-form').on('show.bs.collapse', function() {
            $('#badge-caret').removeClass('glyphicon-triangle-bottom').addClass('glyphicon-triangle-top');
        });
        $('#badge-form').on('hide.bs.collapse', function() {
            $('#badge-caret').removeClass('glyphicon-triangle-top').addClass('glyphicon-triangle-bottom');
        });
    </script>

<!--
    @if($badge_required)
        <div class="panel panel-default">
            <div class="panel-heading">@lang('badge.header')</div>
            <div class="panel-body">
                    {!! BootForm::open(['url' => '/badge/verify']) !!}
                        {!! BootForm::text('code', false) !!}
                        {!! BootForm::submit(trans('badge.btn_verify_code')) !!}
                    {!! BootForm::close() !!}
            </div>
        </div>
    @endif
-->
@endsection
