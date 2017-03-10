@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('auth.logout_header')</div>
        <div class="panel-body">
            {!! BootForm::open(['url' => '/logout_start']) !!}
                @if(count($active_connections) > 0)
                    <p>@lang('auth.logout_hint')</p>
                    @foreach($active_connections as $connection)
                        {!! BootForm::checkbox('providers[]', trans('auth_connections')[$connection->provider], $connection->provider, isset($logout_config[$connection->provider])) !!}
                    @endforeach
                @endif
                {!! BootForm::submit(trans('auth.btn_logout')) !!}
            {!! BootForm::close() !!}
        </div>
    </div>
@endsection