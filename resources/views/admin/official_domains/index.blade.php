@extends('layouts.admin')

@section('content')
    <p>
        <a href="{{ route('admin.official_domains.create') }}" class="btn btn-default">Add</a>
    </p>

    <div class="well">
        {!! BootForm::inline(['method' => 'GET', 'url' => 'admin/official_domains']) !!}
            {!! BootForm::select('country_code', false, $countries, request()->get('country_code')) !!}
            {!! BootForm::text('domain', false, request()->get('domain'), ['placeholder' => 'Domain']) !!}
            {!! BootForm::submit('Filter') !!}
        {!! BootForm::close() !!}
    </div>

    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th>ID</th>
                <th>Country</th>
                <th>Domain</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($models as $model)
                <tr>
                    <td>{{ $model->id }}</td>
                    <td>{{ $model->country_code }}</td>
                    <td>{{ $model->domain }}</td>
                    <td>
                        <a href="{{ route('admin.official_domains.edit', $model->id) }}" class="btn btn-xs btn-primary">Edit</a>
                        <form action="{{ route('admin.official_domains.destroy', $model->id) }}" method="POST" style="display: inline" role="delete">
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