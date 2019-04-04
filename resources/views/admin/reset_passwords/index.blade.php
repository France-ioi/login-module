@extends('layouts.admin')

@section('content')
    {!! BootForm::open(['url' => '/admin/reset_passwords']) !!}
        {!! BootForm::text('password_length', 'Password length', 8) !!}
        {!! BootForm::textarea('logins', 'Logins', null, ['help_text' => 'Enter one login in each line']) !!}
        {!! BootForm::submit() !!}
    {!! BootForm::close() !!}
@endsection