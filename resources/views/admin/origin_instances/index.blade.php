@extends('layouts.admin')

@section('content')
    <p>
        <a href="{{ route('admin.origin_instances.create') }}" class="btn btn-default">Add</a>
    </p>

    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($models as $model)
                <tr>
                    <td>{{ $model->id }}</td>
                    <td>{{ $model->name }}</td>
                    <td>
                        <a href="{{ route('admin.origin_instances.edit', $model->id) }}" class="btn btn-xs btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection