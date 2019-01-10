<ul class="list-group">
    <a href="{{ url('/profile') }}" class="list-group-item">
        @lang('profile.header')
    </a>
    <a href="{{ url('/verification') }}" class="list-group-item">
        @lang('verification.header')
    </a>
    <a href="{{ url('/badges') }}" class="list-group-item">
        @lang('badges.header')
    </a>
    @if(PlatformHelper::needBadgeVerification())
        <a href="{{ url('/badge') }}" class="list-group-item">
            @lang('badge.header')
        </a>
    @endif
    <a href="{{ url('/auth_methods') }}" class="list-group-item">
        @lang('auth_methods.header')
    </a>
</ul>