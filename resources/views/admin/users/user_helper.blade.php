@extends('layouts.admin')

@section('content')
    @include('admin.users.user_info', $user)

    {!! BootForm::open(['url' => '/admin/users/'.$user->id.'/user_helper', 'model' => $user_helper, 'method' => 'POST']) !!}

        <h3>Included platforms</h3>
        @foreach($clients as $client)
            {!! BootForm::checkbox(
                'clients[]',
                $client->name,
                $client->id,
                isset($user_helper_clients[$client->id]))
            !!}
        @endforeach

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


        {!! BootForm::submit() !!}
    {!! BootForm::close() !!}
@endsection