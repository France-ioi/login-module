@extends('layouts.admin')

@section('content')
    <form method="GET" action="{{ url('/admin/users') }}" class="form-inline">
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
        <button type="submit" class="btn btn-primary btn-sm">Find</button>
    </form>

    <hr/>

    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th>ID</th>
                <th>Login</th>
                <th>Name</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->login }}</td>
                    <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                    <td>{{ $user->role }}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Select <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ url('/admin/users/'.$user->id) }}" title="View details">View</a>
                                </li>
                                <li>
                                    <a href="{{ url('/admin/users/'.$user->id.'/emails') }}" title="Send recovery email to user">Send recovery</a>
                                </li>
                                <li>
                                    <a href="{{ url('/admin/users/'.$user->id.'/password') }}" title="Change user password">Password</a>
                                </li>
                                <li>
                                    <a href="{{ url('/admin/users/'.$user->id.'/permissions') }}" title="Change user permissions">Permissions</a>
                                </li>
                            </ul>
                        </div>
                        <form action="{{ url('/admin/users/'.$user->id) }}" method="POST" style="display: inline" role="delete">
                            {{ csrf_field() }}
                            <input name="_method" type="hidden" value="DELETE"/>
                            <button type="submit" class="btn btn-xs btn-danger" role="delete" title="Delete user permanently">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $users->links() }}
@endsection