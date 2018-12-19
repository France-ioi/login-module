@extends('layouts.admin')

@section('content')
    <p>
        {!! BootForm::open(['url' => '/admin/user_helper', 'method' => 'GET', 'class' => 'form-inline']) !!}
            <div class="form-group">
                <label>Keyword</label>
                <input type="text" class="form-control input-sm" name="keyword" value="{{ request()->input('keyword') }}"/>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Find</button>
        {!! BootForm::close() !!}
    </p>

    <hr/>

    @if(count($items))
        <table class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th>Login</th>
                    <th>Name</th>
                    <th>Last connection date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $user)
                    <tr>
                        <td>{{ $user->login }}</td>
                        <td>{{ $user->first_name.' '.$user->last_name }}</td>
                        <td>{{ $user->last_login }}</td>
                        <td>
                            <a href="{{ url('admin/user_helper/'.$user->id.'/details') }}" class="btn btn-xs btn-primary">Details</a>
                            <a href="{{ url('admin/user_helper/'.$user->id.'/profile') }}" class="btn btn-xs btn-primary">Edit</a>
                            <a href="{{ url('admin/user_helper/'.$user->id.'/password') }}" class="btn btn-xs btn-primary">Password</a>
                            <a href="{{ url('admin/user_helper/'.$user->id.'/verification') }}" class="btn btn-xs btn-primary">Verification</a>
                            <form action="{{ url('admin/user_helper/'.$user->id.'/login') }}" method="POST" style="display: inline" role="auth">
                                {{ csrf_field() }}
                                <button type="submit" class="btn btn-xs btn-success" title="Authenticate as this user">Auth</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif(request()->has('keyword'))
        <div class="alert alert-warning">User not found.</div>
    @endif

    <script type="text/javascript">
        $(document).ready(function() {
            $("form[role=auth]").submit(function(e) {
                if(!confirm('Authenticate as this user?')) {
                    e.preventDefault();
                    return false;
                }
            });
        })
    </script>
@endsection