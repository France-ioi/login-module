@extends('layouts.admin')

@section('content')
    <form method="POST" action="{{ url('/admin/users/'.$user->id.'/password') }}" class="form">
        {{ csrf_field() }}
        <div class="form-group">
            <label>New password for user #{{ $user->id }}</label>
            <input type="text" class="form-control" name="password" required="required"/>
        </div>
        <button type="submit" class="btn btn-primary">Change password</button>
    </form>
@endsection