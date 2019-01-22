<ul class="tabs-menu" role="tablist">
    <li>
        <a href="{{ url('/profile') }}" class="{!! Request::is('profile') ? 'active' : '' !!}">
            @lang('profile.header')
        </a>
    </li>
    <li>
        <a href="{{ url('/verification') }}" class="{!! Request::is('verification', 'verification/*') ? 'active' : '' !!}">
            @lang('verification.header')
        </a>
    </li>
    <li>
        <a href="{{ url('/badges') }}">
            @lang('badges.header')
        </a>
    </li>
    @if(PlatformHelper::needBadgeVerification())
        <li>
            <a href="{{ url('/badge') }}" class="{!! Request::is('badge') ? 'active' : '' !!}">
                @lang('badge.header')
            </a>
        </li>
    @endif
    <li>
        <a href="{{ url('/auth_methods') }}" class="{!! Request::is('auth_methods') ? 'active' : '' !!}">
            @lang('auth_methods.header')
        </a>
    </li>
</ul>