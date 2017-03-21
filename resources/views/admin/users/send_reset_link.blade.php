@extends('layouts.admin')

@section('content')
    {!! BootForm::open(['url' => '/admin/users/send_reset_link']) !!}
        {!! BootForm::text('email', 'To', $email->email) !!}
        {!! BootForm::text('subject', 'Subject', $subject) !!}
        {!! BootForm::textArea('body', 'Body', $body) !!}
        {!! BootForm::submit() !!}
    {!! BootForm::close() !!}
@endsection