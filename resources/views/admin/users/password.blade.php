@extends('layouts.admin')

@section('content')
    @include('admin.users.user_info', $user)
    <form method="POST" action="{{ url('/admin/users/'.$user->id.'/password') }}" class="form">
        {{ csrf_field() }}
        <div class="form-group">
            <label>New password</label>
            <input type="text" class="form-control" name="password" required="required"/>
        </div>
        <button type="submit" class="btn btn-primary">Change password</button>
    </form>
@endsection