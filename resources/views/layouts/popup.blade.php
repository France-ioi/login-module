@extends('layouts.app')

@section('navigation')
    <nav class="navbar navbar-default">
        <div class="container">
            @if(Auth::check())
                <p class="navbar-text pull-left">
                    @if(Auth::user()->first_name)
                        {{ Auth::user()->first_name }}
                        {{ Auth::user()->last_name }}
                    @else
                        {{ Auth::user()->login }}
                    @endif
                </p>
            @endif
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">
                        {{ config('app.locales')[app()->getLocale()] }}
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        @foreach(config('app.locales') as $locale => $locale_name)
                            @if($locale != app()->getLocale())
                                <li>
                                    <a href="{{ route('set_locale', ['locale'=>$locale]) }}">
                                        {{ $locale_name }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </li>
                <li>
                    <a href="/badges" className="">@lang('badges.header')</a>
                </li>
                @if(Auth::check())
                    <li>
                        <a href="/logout" className="">@lang('auth.logout')</a>
                    </li>
                @endif
                @if(PlatformHelper::cancelUrl())
                    <li>
                        <a href="{{ PlatformHelper::cancelUrl() }}">@lang('ui.close')</a>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endsection
