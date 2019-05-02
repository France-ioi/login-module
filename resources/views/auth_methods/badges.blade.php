@if(count($badges))
    @foreach($badges as $badge)
        <li class="list-group-item badgeList">
            <div class="pull-right">
                <form method="post" action="/auth_methods/badge_login_ability/{{ $badge->id }}/{{ $badge->login_enabled ? '0' : '1' }}" style="display: inline">
                    {{ csrf_field() }}
                    <button class="btn-link {{ $badge->login_enabled ? 'btn-link-danger' : 'btn-link-primary'}}" type="submit">
                         <i class="fas fa-{{ $badge->login_enabled ? 'times' : 'plus' }} icon"></i> @lang($badge->login_enabled ? 'auth_methods.btn_disable_login' : 'auth_methods.btn_enable_login')
                    </button>
                </form>
            </div>
            @lang('auth_methods.badge_title')
            <span class="label {{ $badge->login_enabled ? 'label-success' : 'label-default' }}">
                @if($badge->login_enabled)
                    <i class="fas fa-check"></i>
                @endif
                @lang($badge->login_enabled ? 'auth_methods.label_enabled_login' : 'auth_methods.label_disabled_login')
                {{ $badge->name }}
            </span>
            <div class="selfToggleItem">
                <label>
                    <input type="checkbox" />
                    <span class="toggle-btn"><i class="fas fa-eye icon"></i> @lang('auth_methods.badge_code_toggle')</span> <span class="code">{{ $badge->code }} <i class="fas fa-eye-slash icon"></i></span>
                </label>
            </div>
        </li>
    @endforeach
@endif