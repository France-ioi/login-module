<div class="panel panel-default">
    <div class="alert-section">
        <div class="alert alert-danger">
            <i class="fas fa-bell icon"></i>
            @lang('auth_methods.alert')
        </div>
    </div>
    <div class="panel-body">
        <div class="sectionTitle">
            <i class="fas fa-unlock-alt icon"></i>
            @lang('auth_methods.title')
        </div>
        <div class="panel-content">
            <ul class="list-group data-table">
                <li class="list-group-item">
                    {{ trans('auth_methods.password') }}
                    @if($has_password)
                        <span class="label label-success">
                            <i class="fas fa-check"></i>
                            @lang('auth_methods.active')
                        </span>
                    @endif
                    <button type="button" class="btn-link pull-right" data-toggle="modal" data-target="#edit-password">
                        <i class="fas fa-pencil-alt icon"></i>
                        @lang($has_password ? 'auth_methods.btn_change' : 'auth_methods.btn_add')
                    </button>
                </li>
                @foreach($providers as $provider)
                    <li class="list-group-item">
                        {{ trans('auth_connections')[$provider] }}
                        @if(isset($connected[$provider]))
                            <span class="label label-success">
                                <i class="fas fa-check"></i>
                                @lang('auth_methods.active')
                            </span>
                            @if(isset($support_remove[$provider]))
                                <form method="post" action="/oauth_client/remove/{{ $provider }}" style="display: inline">
                                    {{ csrf_field() }}
                                    <button class="btn-link btn-danger pull-right" type="submit">
                                        <i class="fas fa-trash-alt icon"></i>
                                        @lang('auth_methods.btn_remove')
                                    </button>
                                </form>
                            @endif

                        @else
                            <a class="btn-link pull-right" href="/oauth_client/redirect/{{ $provider }}">
                                <i class="fas fa-plus icon"></i>
                                @lang('auth_methods.btn_add')
                            </a>
                        @endif
                    </li>
                @endforeach
                @if(count($badges))
                    @foreach($badges as $badge)
                        <li class="list-group-item badgeList">
                            @lang('auth_methods.badge_title')
                            <span class="label {{ $badge->login_enabled ? 'label-success' : 'label-default' }}">
                                @if($badge->login_enabled)
                                    <i class="fas fa-check"></i>
                                @endif
                                @lang($badge->login_enabled ? 'auth_methods.label_enabled_login' : 'auth_methods.label_disabled_login')
                                {{ $badge->name }}
                            </span>
                            <div class="pull-right">
                                <span class="selfToggleItem">
                                    <label>
                                        <input type="checkbox" />
                                        <span class="toggle-btn">@lang('auth_methods.badge_code_toggle') <i class="fas fa-eye icon"></i></span> <span class="code">{{ $badge->code }} <i class="fas fa-eye-slash icon"></i></span>
                                    </label>
                                </span>
                                <form method="post" action="/auth_methods/badge_login_ability/{{ $badge->id }}/{{ $badge->login_enabled ? '0' : '1' }}" style="display: inline">
                                    {{ csrf_field() }}
                                    <button class="btn-link {{ $badge->login_enabled ? 'btn-link-danger' : 'btn-link-primary'}}" type="submit">
                                         <i class="fas fa-{{ $badge->login_enabled ? 'times' : 'plus' }} icon"></i> @lang($badge->login_enabled ? 'auth_methods.btn_disable_login' : 'auth_methods.btn_enable_login')
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="edit-password">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">+</span>
                </button>
                <div class="sectionTitle">
                    <i class="fas fa-shield-alt icon"></i>
                    @lang('password.header')
                </div>
            </div>
            <div class="modal-body">
                <div class="modal-body-content">
                    @include('password.index')
                </div>
            </div>
        </div>
    </div>
</div>