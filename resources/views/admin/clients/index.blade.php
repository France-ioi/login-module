@extends('layouts.admin')

@section('content')
    <p>
        <a href="{{ route('admin.clients.create') }}" class="btn btn-default">Add</a>
    </p>

    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $client)
                <tr>
                    <td>{{ $client->id }}</td>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->revoked ? 'Revoked' : 'Active'}}</td>
                    <td>
                        <a href="{{ url('/admin/clients/'.$client->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
                        <form action="{{ url('/admin/clients/'.$client->id) }}" method="POST" style="display: inline" role="delete">
                            {{ csrf_field() }}
                            <input name="_method" type="hidden" value="DELETE"/>
                            <button type="submit" class="btn btn-xs btn-danger" role="delete">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script type="text/javascript">
        $("form[role=delete]").submit(function(e) {
            if(!confirm('Are you sure you want to delete this client?')) {
                e.preventDefault();
                return false;
            }
        });
    </script>
@endsection