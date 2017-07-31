@extends('layouts.admin')

@section('content')
    @include('admin.users.user_info', $user)
    <form method="POST" action="{{ url('/admin/users/'.$user->id.'/teacher_status') }}" class="form">
        {{ csrf_field() }}
        {!! BootForm::open(['url' => '/admin/users/'.$user->id.'/teacher_status']) !!}
            {!! BootForm::checkbox('teacher_verified', 'Verified teacher', null, $user->teacher_verified) !!}
            {!! BootForm::submit() !!}
        {!! BootForm::close() !!}
@endsection