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
            <a href="{{ url('/verification') }}" class="list-group-item">
                @lang('verification.header')
            </a>
            @if($need_badge_verification)
                <a href="{{ url('/badge') }}" class="list-group-item">
                    @lang('badge.header')
                </a>
            @endif
            <a href="{{ url('/auth_methods') }}" class="list-group-item">
                @lang('auth_methods.header')
            </a>
        </ul>
    </div>
@endsection