@extends('layouts.admin')

@section('content')
    <h2>
        @if($client->exists())
            Client ID: {{ $client->id }}
        @else
            New client
        @endif
    </h2>
    {!! BootForm::open(['model' => $client, 'store' => 'admin.clients.store', 'update' => 'admin.clients.update']) !!}
        {!! BootForm::text('name', 'Name') !!}
        {!! Bootform::hidden('revoked', 0) !!}
        {!! BootForm::checkbox('revoked', 'Revoked') !!}
        {!! BootForm::text('secret', 'Secret') !!}
        {!! BootForm::text('redirect', 'Redirect') !!}
        {!! BootForm::text('badge_url', 'Badge URL') !!}
        <label>Required fields</label>
        <div class="row">
            @foreach($profile_fields as $field)
                <div class="col-sm-2 col-xs-6">
                    {!! BootForm::checkbox('profile_fields[]', $field, $field, is_array($client->profile_fields) && in_array($field, $client->profile_fields)) !!}
                </div>
            @endforeach
        </div>
        <label>Auth config</label>
        <div>
            @foreach($providers as $provider)
                {!! BootForm::checkbox('auth_order[]', $provider, $provider, is_array($client->auth_order) && in_array($provider, $client->auth_order)) !!}
            @endforeach
        </div>
        {!! BootForm::checkbox('autoapprove_authorization', 'Auto-approve authorizations from PMS to this platform') !!}
        {!! BootForm::textArea('public_key', 'Public key (LTI)') !!}
        {!! BootForm::submit() !!}
    {!! BootForm::close() !!}
@endsection
