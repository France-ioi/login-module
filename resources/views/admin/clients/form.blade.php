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
        {!! BootForm::checkbox('badge_required', 'Badge code required.') !!}
        {!! BootForm::checkbox('badge_autologin', 'Login with a badge code, without asking for a login and password.') !!}
        <label>Required user attributes</label>
        <div class="row">
            @foreach($user_attributes as $attr)
                <div class="col-sm-4 col-xs-6">
                    {!! BootForm::checkbox('user_attributes[]', $attr, $attr, in_array($attr, $client->user_attributes)) !!}
                </div>
            @endforeach
        </div>
        <label>Recommended user attributes</label>
        <div class="row">
            @foreach($user_attributes as $attr)
                <div class="col-sm-4 col-xs-6">
                    {!! BootForm::checkbox('recommended_attributes[]', $attr, $attr, in_array($attr, $client->recommended_attributes)) !!}
                </div>
            @endforeach
        </div>
        <label>Login page auth methods setup (drag&drop to reorder)</label>
        <div>
            <ul class="list-group list-group-sortable" id="auth-order">
                @foreach($auth_methods as $method)
                    <li class="list-group-item">
                        <input type="hidden" name="auth_order[]" value="{{ $method }}"/>
                        {{ $method == '_' ? '-- Hidden methods divider --' : $method}}
                    </li>
                @endforeach
            </ul>
        </div>
        <label>Auth config</label>
        {!! BootForm::checkbox('autoapprove_authorization', 'Auto-approve authorizations to this platform') !!}
        {!! BootForm::textArea('public_key', 'Public key (LTI)') !!}
        {!! BootForm::submit() !!}
    {!! BootForm::close() !!}

    <style type="text/css">
        .list-group-sortable li {
            cursor: move;
        }
    </style>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#auth-order').sortable();
        })
    </script>
@endsection
