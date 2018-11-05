@extends('layouts.admin')

@section('content')
    <h2>
        @if($badge_api->exists())
            ID: {{ $badge_api->id }}
        @else
            New record
        @endif
    </h2>

    {!! BootForm::open(['model' => $badge_api, 'store' => 'admin.badge_apis.store', 'update' => 'admin.badge_apis.update']) !!}
        {!! BootForm::text('name') !!}
        {!! BootForm::text('url') !!}
        {!! BootForm::hidden('auth_enabled', 0) !!}
        {!! BootForm::checkbox('auth_enabled', 'Can be used as an authentication method') !!}
        {!! BootForm::submit() !!}
    {!! BootForm::close() !!}

@endsection
