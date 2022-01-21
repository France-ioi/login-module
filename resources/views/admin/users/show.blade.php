@extends('layouts.admin')

@section('content')
    <h2>User</h2>
    @include('admin.users.attributes_table', ['model' => $user])

    @if($user->auth_connections && count($user->auth_connections))
        <h2>Auth connections</h2>
        @foreach($user->auth_connections as $model)
            @include('admin.users.attributes_table', ['model' => $model])
        @endforeach
    @endif

    @if($user->badges && count($user->badges))
        <h2>Badges</h2>
        @foreach($user->badges as $model)
            @include('admin.users.attributes_table', ['model' => $model])
        @endforeach
    @endif
@endsection