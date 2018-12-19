@extends('layouts.admin')

@section('content')
    <a class="btn btn-default" onclick="window.history.back()">Go back</a>
    <h3>User password</h3>
    @include('admin.users.user_info', $user)
    {!! BootForm::open(['/admin/user_helper/'.$user->id.'/password']) !!}
        {!! BootForm::text('password', 'New password') !!}
        {!! BootForm::submit('Save') !!}
    {!! BootForm::close() !!}
@endsection