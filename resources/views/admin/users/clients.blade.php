@extends('layouts.admin')

@section('content')
    <p>
        User #{{ $user->id }}, {{ $user->first_name }}  {{ $user->last_name }} linked clients:
    </p>

    {!! BootForm::open(['url' => '/admin/users/'.$user->id.'/clients']) !!}
        <table class="table">
            <tr>
                <th>ID</th>
                <th>Client name</th>
                <th>Last activity</th>
                <th>Is admin</th>
            </tr>
            @foreach($user->clients as $client)
                <tr>
                    <td>{{ $client->id }}</td>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->pivot->last_activity }}</td>
                    <td>
                        <input type="checkbox" name="is_admin[{{ $client->id }}]" {{ $client->pivot->admin ? 'checked="checked"' : ''}} />
                    </td>
                </tr>
            @endforeach
        </table>
        {!! BootForm::submit() !!}
    {!! BootForm::close() !!}    
@endsection