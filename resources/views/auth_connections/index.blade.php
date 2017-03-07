@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('auth_connections.header')
        </div>

        <ul class="list-group">
            @foreach($providers as $provider)
                <li class="list-group-item">
                    {{ trans('auth_connections')[$provider] }}
                    @if(isset($connected[$provider]))
                        <form method="post" action="/oauth_client/remove/{{ $provider }}" style="display: inline">
                            {{ csrf_field() }}
                            <button class="btn btn-xs btn-danger pull-right" type="submit">@lang('auth_connections.btn_remove')</button>
                        </form>
                    @else
                        <a class="btn btn-xs btn-primary pull-right" href="/oauth_client/redirect/{{ $provider }}">@lang('auth_connections.btn_add')</a>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@endsection