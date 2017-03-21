@extends('layouts.admin')

@section('content')
    {!! BootForm::open(['model' => $client, 'store' => 'admin.client.store', 'update' => 'admin.client.update']) !!}
        {!! BootForm::text('name', 'Name') !!}
        {!! BootForm::text('secret', 'Secret') !!}
        {!! BootForm::text('redirect', 'Redirect') !!}
        {!! BootForm::checkbox('revoked', 'Revoked') !!}

        @foreach($profile_fields as $field)
            {!! BootForm::checkbox('revoked', 'Revoked') !!}
            {!! BootForm::checkbox('profile_fields[]', $field, $field, isset($client->profile$fieldlogout_config[$connection->provider])) !!}
        @endforeach



    {!! BootForm::close() !!}
@endsection