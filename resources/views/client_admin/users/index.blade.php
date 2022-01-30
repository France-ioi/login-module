@extends('layouts.client_admin')

@section('content')
    <form method="GET" action="/client_admin/{{ $client->id }}/users">
        <div class="form-group">
            <label>User ID</label>
            <input type="text" class="form-control input-sm" name="id" value="{{ request()->input('id') }}"/>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="text" class="form-control input-sm" name="email" value="{{ request()->input('email') }}"/>
        </div>
        <div class="form-group">
            <label>Login</label>
            <input type="text" class="form-control input-sm" name="login" value="{{ request()->input('login') }}"/>
        </div>
        <div class="form-group">
            <label>First name</label>
            <input type="text" class="form-control input-sm" name="first_name" value="{{ request()->input('first_name') }}"/>
        </div>
        <div class="form-group">
            <label>Last  name</label>
            <input type="text" class="form-control input-sm" name="last_name" value="{{ request()->input('last_name') }}"/>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        <a class="btn btn-primary btn-sm" href="/client_admin/{{ $client->id }}/users">Reset</a>
    </form>


    <hr/>

    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th>ID</th>
                <th>Login</th>
                <th>Email</th>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->login }}</td>
                    <td>{{ $user->primary_email }} <br/> {{ $user->secondary_email }}</td>
                    <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Select <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="/client_admin/{{ $client->id }}/users/{{ $user->id }}/verification?refer_page={{ $refer_page }}">Verification</a>
                                </li>
                                <li>
                                    <a href="/client_admin/{{ $client->id }}/users/{{ $user->id }}/ban?refer_page={{ $refer_page }}">Ban</a>
                                </li>                        
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $users->links() }}
@endsection