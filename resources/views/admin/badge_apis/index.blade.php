@extends('layouts.admin')

@section('content')
    <p>
        <a href="{{ route('admin.badge_apis.create') }}" class="btn btn-default">Add</a>
    </p>

    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th>Name</th>
                <th>Url</th>
                <th>Auth method</th>
            </tr>
        </thead>
        <tbody>
            @foreach($badge_apis as $badge_api)
                <tr>
                    <td>{{ $badge_api->name }}</td>
                    <td>{{ $badge_api->url }}</td>
                    <td>{{ $badge_api->auth_enabled ? 'yes' : 'no' }}</td>
                    <td>
                        <a href="{{ route('admin.badge_apis.edit', $badge_api->id) }}" class="btn btn-xs btn-primary">Edit</a>
                        <form action="{{ route('admin.badge_apis.destroy', $badge_api->id) }}" method="POST" style="display: inline" role="delete">
                            {{ csrf_field() }}
                            <input name="_method" type="hidden" value="DELETE"/>
                            <button type="submit" class="btn btn-xs btn-danger" role="delete">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>


@endsection