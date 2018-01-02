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
    <li class="list-group-item">
    {!! BootForm::horizontal(['url' => '/badge/verify']) !!}
        {!! BootForm::text('code', trans('badge.header')) !!}
        {!! BootForm::submit(trans('badge.btn_verify_code')) !!}
    {!! BootForm::close() !!}
    </li>
@endpush

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('auth.login_header')</div>
        <div class="panel-body">
            <div class="list-group">
            @foreach($auth_visible as $method)
                @stack($method)
            @endforeach
            </div>

            @if(count($auth_hidden) > 0)
                <hr>
                <button id="btn-show-hidden" class="btn btn-block btn-link">Show more</button>
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
        })
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
