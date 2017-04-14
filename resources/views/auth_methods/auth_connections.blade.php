<div class="panel panel-default">
    <div class="panel-heading">
        @lang('auth_methods.header')
    </div>

    <ul class="list-group">
        @foreach($providers as $provider)
            <li class="list-group-item">
                {{ trans('auth_connections')[$provider] }}
                @if(isset($connected[$provider]))
                    <span class="label label-success">@lang('auth_methods.active')</span>
                    @if(isset($support_remove[$provider]))
                        <form method="post" action="/oauth_client/remove/{{ $provider }}" style="display: inline">
                            {{ csrf_field() }}
                            <button class="btn btn-xs btn-danger pull-right" type="submit">@lang('auth_methods.btn_remove')</button>
                        </form>
                    @endif

                @else
                    <a class="btn btn-xs btn-primary pull-right" href="/oauth_client/redirect/{{ $provider }}">@lang('auth_methods.btn_add')</a>
                @endif
            </li>
        @endforeach
        <li class="list-group-item">
            {{ trans('auth_methods.password') }}
            @if($has_password)
                <span class="label label-success">@lang('auth_methods.active')</span>
            @endif
            <a class="btn btn-xs btn-primary pull-right" href="/password">
                @lang($has_password ? 'auth_methods.btn_change' : 'auth_methods.btn_add')
            </a>
        </li>
    </ul>
</div>