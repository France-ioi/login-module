@extends('layouts.admin')

@section('content')
    <div class="alert alert-info">
        Default login prefix: <strong>{{ config('lti.default_login_prefix')}}</strong>
    </div>

    <p>
        <a href="{{ route('admin.lti_configs.create') }}" class="btn btn-default">Add</a>
    </p>

    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th>Consumer key</th>
                <th>Login prefix</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lti_configs as $lti_config)
                <tr>
                    <td>{{ $lti_config->lti_consumer_key }}</td>
                    <td>{{ $lti_config->prefix }}</td>
                    <td>
                        <a href="{{ route('admin.lti_configs.edit', $lti_config->id) }}" class="btn btn-xs btn-primary">Edit</a>
                        <form action="{{ route('admin.lti_configs.destroy', $lti_config->id) }}" method="POST" style="display: inline" role="delete">
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
            if(!confirm('Are you sure you want to delete this consumer?')) {
                e.preventDefault();
                return false;
            }
        });
    </script>
@endsection