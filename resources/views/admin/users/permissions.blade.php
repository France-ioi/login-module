@extends('layouts.admin')

@section('content')
    @include('admin.users.user_info', $user)
    {!! BootForm::open(['url' => '/admin/users/'.$user->id.'/permissions']) !!}
        {!! BootForm::checkboxes('roles[]', 'Roles', $roles, $user->roles->pluck('name')->toArray()) !!}
        {!! BootForm::checkboxes('permissions[]', 'Permissions', $permissions, $user->permissions->pluck('name')->toArray()) !!}
        {!! BootForm::submit() !!}
    {!! BootForm::close() !!}
@endsection