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
                    <button class="btn-link btn-link-danger pull-right" type="submit">
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
