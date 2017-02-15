@extends('layouts.admin')

@section('content')
    <form method="GET" action="{{ url('/admin/users') }}" class="form-inline">
        <div class="form-group">
            <label>User ID</label>
            <input type="text" class="form-control" name="id" value="{{ request()->input('id') }}"/>
        </div>
        <button type="submit" class="btn btn-primary">Find</button>
    </form>

    <hr/>

    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th>ID</th>
                <th>Role</th>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->role }}</td>
                    <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                    <td>
                        <a href="{{ url('/admin/users/'.$user->id.'/password') }}" class="btn btn-xs btn-primary">Password</a>
                        <form action="{{ url('/admin/users/'.$user->id) }}" method="POST" style="display: inline">
                            {{ csrf_field() }}
                            <input name="_method" type="hidden" value="DELETE"/>
                            <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $users->links() }}
@endsection