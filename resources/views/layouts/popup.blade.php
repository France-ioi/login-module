@extends('layouts.app')

@section('navigation')
    <nav class="navbar navbar-default">
        <div class="container">
            @if(Auth::check())
                <p class="navbar-text">
                    @if(Auth::user()->first_name)
                        {{ Auth::user()->first_name }}
                        {{ Auth::user()->last_name }}
                    @else
                        {{ Auth::user()->login }}
                    @endif
                </p>
            @endif
            <div class="navbar-header pull-right">
                <ul class="nav pull-left">
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
                </ul>
                @if(Auth::check() && Auth::user()->admin)
                    <ul class="nav pull-left">
                        <li>
                            <a href="/logout" className="">Logout</a>
                        </li>
                    </ul>
                @endif
            </div>
        </div>
    </nav>
@endsection