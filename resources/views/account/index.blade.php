@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('account.header')
        </div>
        <ul class="list-group">
            <a href="{{ url('/profile') }}" class="list-group-item">
                @lang('profile.header')
            </a>
            @if($need_badge_verification)
                <a href="{{ url('/badge') }}" class="list-group-item">
                    @lang('badge.header')
                </a>
            @endif
            @if($need_email_verification)
                <a href="{{ url('/email_verification') }}" class="list-group-item">
                    @lang('email_verification.header')
                </a>
            @endif
            <a href="{{ url('/auth_connections') }}" class="list-group-item">
                @lang('auth_connections.header')
            </a>
            <a href="{{ url('/password') }}" class="list-group-item">
                @lang('password.header')
            </a>
        </ul>
    </div>
@endsection