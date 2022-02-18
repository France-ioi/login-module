@extends('layouts.client_admin')

@section('content')
    <p>
        <a class="btn btn-primary" href="/client_admin/{{ $client->id }}/export/users" target="_blank">Export users</a>
    </p>

    <form method="GET" action="/client_admin/{{ $client->id }}/users">
        @if(request()->has('sort_field'))
            <input type="hidden" name="sort_field" value="{{ request()->get('sort_field') }}">
        @endif
        @if(request()->has('sort_order'))
            <input type="hidden" name="sort_order" value="{{ request()->get('sort_order') }}">
        @endif        
        <div class="form-group">
            <label>User ID</label>
            <input type="text" class="form-control input-sm" name="id" value="{{ request()->get('id') }}"/>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="text" class="form-control input-sm" name="email" value="{{ request()->get('email') }}"/>
        </div>
        <div class="form-group">
            <label>Login</label>
            <input type="text" class="form-control input-sm" name="login" value="{{ request()->get('login') }}"/>
        </div>
        <div class="form-group">
            <label>First name</label>
            <input type="text" class="form-control input-sm" name="first_name" value="{{ request()->get('first_name') }}"/>
        </div>
        <div class="form-group">
            <label>Last  name</label>
            <input type="text" class="form-control input-sm" name="last_name" value="{{ request()->get('last_name') }}"/>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="teacher_not_verified" {{ request()->has('teacher_not_verified') ? 'checked="checked"' : '' }}"/>
                Teacher role not verified
            </label>
        </div>        
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        <a class="btn btn-primary btn-sm" href="/client_admin/{{ $client->id }}/users">Reset</a>
    </form>


    <hr/>

    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th>{!! SortableTable::th('id', 'ID') !!}</th>
                <th>{!! SortableTable::th('created_at', 'Created at') !!}</th>
                <th>{!! SortableTable::th('last_activity', 'Last activity') !!}</th>
                <th>{!! SortableTable::th('login', 'Login') !!}</th>
                <th>{!! SortableTable::th('emails', 'Email') !!}</th>
                <th>{!! SortableTable::th('name', 'Name') !!}</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->created_at }}</td>
                    <td>{{ $row->last_activity }}</td>
                    <td>{{ $row->login }}</td>
                    <td>{!! nl2br($row->emails) !!}</td>
                    <td>{{ $row->name }}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Select <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="/client_admin/{{ $client->id }}/users/{{ $row->id }}/edit?refer_page={{ $refer_page }}">Edit</a>
                                </li>                                
                                <li>
                                    <a href="/client_admin/{{ $client->id }}/users/{{ $row->id }}/verification?refer_page={{ $refer_page }}">Verification</a>
                                </li>
                                <li>
                                    <a href="/client_admin/{{ $client->id }}/users/{{ $row->id }}/ban?refer_page={{ $refer_page }}">Ban</a>
                                </li>
                                <li>
                                    <a href="/client_admin/{{ $client->id }}/users/{{ $row->id }}/password?refer_page={{ $refer_page }}">Password</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $rows->links() }}
@endsection