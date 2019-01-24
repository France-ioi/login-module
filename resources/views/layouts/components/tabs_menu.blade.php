<ul class="tabs-menu" role="tablist">
    <li class="{!! Request::is('profile') ? 'active' : '' !!}">
        <a href="{{ url('/profile') }}">
            <i class="fas fa-user"></i>
            @lang('profile.header')
        </a>
    </li>
    <li class="{!! Request::is('verification', 'verification/*') ? 'active' : '' !!}">
        <a href="{{ url('/verification') }}">
            <i class="fas fa-check"></i>
            @lang('verification.header')
        </a>
    </li>
    <li>
        <a href="{{ url('/badges') }}">
            <i class="fas fa-qrcode"></i>
            @lang('badges.header')
        </a>
    </li>
    @if(PlatformHelper::needBadgeVerification())
        <li class="{!! Request::is('badge') ? 'active' : '' !!}">
            <a href="{{ url('/badge') }}">
                <i class="fas fa-qrcode"></i>
                @lang('badge.header')
            </a>
        </li>
    @endif
    <li class="{!! Request::is('auth_methods') ? 'active' : '' !!}">
        <a href="{{ url('/auth_methods') }}">
            <i class="fas fa-unlock-alt"></i>
            @lang('auth_methods.header')
        </a>
    </li>
    <li class="{!! Request::is('collected_data') ? 'active' : '' !!}">
        <a href="{{ url('/collected_data') }}">
            <i class="fas fa-shield-alt"></i>
            @lang('collected_data.header')
        </a>
    </li>
</ul>

<script type="text/javascript">
    // Auto-focus either login or password field
    $(function() {
        // Add prev and next classes to .current direct siblings
      $('.tabs-menu li').removeClass('prev next');
      $('.tabs-menu li.active').prev().addClass('prev');
      $('.tabs-menu li.active').next().addClass('next');
    });
</script>