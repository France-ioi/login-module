@extends('layouts.admin')

@section('content')
    <h2>User</h2>
    @include('admin.users.attributes_table', ['model' => $user])

    @if(count($user->authConnections))
        <h2>Auth connections</h2>
        @foreach($user->authConnections as $model)
            @include('admin.users.attributes_table', ['model' => $model])
        @endforeach
    @endif

    @if(count($user->badges))
        <h2>Badges</h2>
        @foreach($user->badges as $model)
            @include('admin.users.attributes_table', ['model' => $model])
        @endforeach
    @endif
@endsection