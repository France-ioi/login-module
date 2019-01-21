@extends('layouts.app')

@section('navigation')
    <div class="header">
        <div class="container">
            <div class="headerTop clearfix">
                <div class="platformTitle pull-left">{{ PlatformHelper::platformName() }}</div>
                <div class="pull-right headerNav">
                    <div class="dropdown bgDropdown">
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
                    </div>
                    @if(Auth::check())
                        <div>
                            <a href="/logout" className=""><i class="fas fa-power-off">X</i></a>
                        </div>
                    @endif
                    @if(PlatformHelper::cancelUrl())
                        <div>
                            <a href="{{ PlatformHelper::cancelUrl() }}">@lang('ui.close')</a>
                        </div>
                    @endif
                </div>
            </div>
            @if(Auth::check())
                <div class="pageTitle">
                    @if(Auth::user()->real_name_visible)
                        {{ Auth::user()->first_name }}
                        {{ Auth::user()->last_name }}
                    @else
                        {{ Auth::user()->login }}
                    @endif
                </div>

                @include('layouts.components.tabs_menu')
            @endif

        </div>
    </div>
@endsection
