@if(count($badges))
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('auth_methods.badges_header')
        </div>

        <ul class="list-group">
            @foreach($badges as $badge)
                <li class="list-group-item">
                    {{ $badge->code }}
                    <form method="post" action="/auth_methods/badge_login_ability/{{ $badge->id }}/{{ $badge->login_enabled ? '0' : '1' }}" style="display: inline">
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