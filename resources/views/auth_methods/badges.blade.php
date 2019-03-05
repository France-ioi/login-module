@if(count($badges))
    <div class="sectionTitle">
        @lang('auth_methods.badges_header')
    </div>
    <div class="panel-content">
        <ul class="list-group">
            @foreach($badges as $badge)
                <li class="list-group-item">
                    @lang('auth_methods.badges_header')
                    {{ $badge->code }}
                    <form method="post" action="/auth_methods/badge_login_ability/{{ $badge->id }}/{{ $badge->login_enabled ? '0' : '1' }}" style="display: inline">
                        {{ $badge->name }}
                        {{ csrf_field() }}
                        <button class="btn btn-xs pull-right {{ $badge->login_enabled ? 'btn-danger' : 'btn-primary'}}" type="submit">
                            @lang($badge->login_enabled ? 'auth_methods.btn_disable_login' : 'auth_methods.btn_enable_login')
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>
@endif