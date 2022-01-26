@extends('layouts.pages.customer')

@include('layouts.components.tabs_menu')

@section('header')
    @if(Auth::check())
        <div class="container">
            <div class="userHeader pageTitle">
                <img src="{!! Auth::user()->picture !!}" class="user-picture"/>
                @if(Auth::user()->real_name_visible)
                    {{ Auth::user()->first_name }}
                    {{ Auth::user()->last_name }}
                @else
                    {{ Auth::user()->login }}
                @endif
            </div>
            @yield('tabs_menu')
        </div>
    @endif
@endsection

@section('navigation')
    <div class="header">
        <div class="headerTop clearfix">
            <div class="platformTitle pull-left">{{ PlatformHelper::platformName() }}</div>
            <div class="pull-right headerNav">
                <div class="dropdown bgDropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">
                        {{ config('app.locales')[app()->getLocale()] }}
                        <i class="fas fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        @foreach(config('app.locales') as $locale => $locale_name)
                            @if($locale != app()->getLocale())
                                <li>
                                    <a href="{{ route('set_locale', ['locale'=>$locale]) }}" class="link-set-locale">
                                        {{ $locale_name }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                @if(Auth::check())
                    <div class="logout">
                        <a href="/logout"><i class="fas fa-power-off"></i></a>
                    </div>
                @endif
                @if(PlatformHelper::cancelUrl())
                    <div>
                        <a href="{{ PlatformHelper::cancelUrl() }}">@lang('ui.close')</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div style="display: none" id="set-locale-confirmation">@lang('ui.locale_onchange_confirmation')</div>

    <script>
        $(document).ready(function(e) {
            $('a.link-set-locale').on('click', function(e) {
                e.preventDefault();
                var link = $(this);
                var url = link.attr('href');
                if(window.user_logged) {
                    var text = $('#set-locale-confirmation').text();
                    text = text.replace(':locale', link.text().trim())
                    if(confirm(text)) {
                        url += '?update_user=1';
                    }
                }
                location.href = url;
            })
        })
        
    </script>
@endsection
