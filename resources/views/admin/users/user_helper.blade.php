@extends('layouts.admin')

@section('content')
    @include('admin.users.user_info', $user)

    {!! BootForm::open(['url' => '/admin/users/'.$user->id.'/user_helper', 'model' => $user_helper, 'method' => 'POST']) !!}

        <h3>Included platforms</h3>
        <div class="row">
            @foreach($clients as $client)
                <div class="col-sm-4 col-xs-12">
                    {!! BootForm::checkbox(
                        'clients[]',
                        $client->name,
                        $client->id,
                        isset($user_helper_clients[$client->id]))
                    !!}
                </div>
            @endforeach
        </div>

        <h3>Options</h3>
        {!! BootForm::text('searches_amount', 'Number of searches allowed per day') !!}
        {!! BootForm::text('changes_amount', 'Number of changes allowed per day') !!}

        <h3>Permissions</h3>
        <div class="row">
            @foreach($user_attributes as $attr)
                <div class="col-sm-2 col-xs-6">
                    {!! BootForm::select('user_attributes['.$attr.']', $attr, [
                        '' => '',
                        'read' => 'View',
                        'write' => 'Update'
                    ]) !!}
                </div>
            @endforeach
        </div>

        <h3>Allow to verify</h3>
        <div class="row">
            @foreach($all_verifiable_attributes as $attr)
                <div class="col-sm-2 col-xs-6">
                    {!! BootForm::checkbox(
                        'verifiable_attributes[]',
                        $attr,
                        $attr,
                        in_array($attr, $user_helper->verifiable_attributes)
                    ) !!}
                </div>
            @endforeach
        </div>

        {!! BootForm::submit() !!}
    {!! BootForm::close() !!}
@endsection