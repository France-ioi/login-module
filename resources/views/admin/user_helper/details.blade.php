@extends('layouts.admin')

@section('content')
    <a class="btn btn-default" onclick="window.history.back()">Go back</a>
    <h3>User auth details</h3>
    @include('admin.users.user_info', $user)

    <h3>Platforms where user has authenticated on at least once</h3>
    @if(count($user->accessTokenCounters))
        <ul>
            @foreach($user->accessTokenCounters as $counter)
                <li>{{ $counter->client->name }}</li>
            @endforeach
        </ul>
    @else
        None
    @endif

    <h3>Used authentication methods</h3>
    @if(count($user->authConnections))
        <ul>
            @foreach($user->authConnections as $connection)
                <li>{{ trans('auth_connections')[$connection->provider] }}</li>
            @endforeach
        </ul>
    @else
        None
    @endif


    <h3>Badges</h3>
    @if(count($user->badges))
        <ul>
            @foreach($user->badges as $badge)
                <li>{{ $badge->badgeApi->name  }}</li>
            @endforeach
        </ul>
    @else
        None
    @endif

@endsection