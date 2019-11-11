@section('tabs_menu')
    <ul class="tabs-menu" role="tablist">
        @if(PlatformHelper::navTabVisible('profile'))
            <li class="{!! Request::is('profile') ? 'active' : '' !!}">
                <a href="{{ url('/profile') }}">
                    <i class="fas fa-user"></i>
                    @lang('profile.header')
                </a>
            </li>
        @endif
        @if(PlatformHelper::navTabVisible('verification'))
            <li class="{!! Request::is('verification', 'verification/*') ? 'active' : '' !!}">
                <a href="{{ url('/verification') }}">
                    <i class="fas fa-check"></i>
                    @lang('verification.header')
                </a>
            </li>
        @endif
        @if(PlatformHelper::navTabVisible('badges'))
            <li class="{!! Request::is('badges', 'badges/*') ? 'active' : '' !!}">
                <a href="{{ url('/badges') }}">
                    <i class="fas fa-qrcode"></i>
                    @lang('badges.header')
                </a>
            </li>
        @endif
        @if(PlatformHelper::navTabVisible('badge'))
            <li class="{!! Request::is('badge') ? 'active' : '' !!}">
                <a href="{{ url('/badge') }}">
                    <i class="fas fa-qrcode"></i>
                    @lang('badge.header')
                </a>
            </li>
        @endif
        @if(PlatformHelper::navTabVisible('auth_methods'))
            <li class="{!! Request::is('auth_methods') ? 'active' : '' !!}">
                <a href="{{ url('/auth_methods') }}">
                    <i class="fas fa-unlock-alt"></i>
                    @lang('auth_methods.header')
                </a>
            </li>
        @endif
        @if(PlatformHelper::navTabVisible('collected_data'))
            <li class="{!! Request::is('collected_data') ? 'active' : '' !!}">
                <a href="{{ url('/collected_data') }}">
                    <i class="fas fa-shield-alt"></i>
                    @lang('collected_data.header')
                </a>
            </li>
        @endif
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
@endsection