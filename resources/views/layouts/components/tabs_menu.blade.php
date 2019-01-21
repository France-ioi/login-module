<ul class="tabs-menu" role="tablist">
    <li>
        <a href="{{ url('/profile') }}">
            @lang('profile.header')
        </a>
    </li>
    <li>
        <a href="{{ url('/verification') }}">
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
            <a href="{{ url('/badge') }}">
                @lang('badge.header')
            </a>
        </li>
    @endif
    <li>
        <a href="{{ url('/auth_methods') }}">
            @lang('auth_methods.header')
        </a>
    </li>
</ul>