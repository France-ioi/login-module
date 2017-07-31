@extends('layouts.admin')

@section('content')
    @if(session('last_email_plain_text'))
        <pre>{{ session('last_email_plain_text') }}</pre>
    @endif

    @if(count($user->emails))
        @include('admin.users.user_info', $user)
        {!! BootForm::open(['url' => '/admin/users/create_reset_link']) !!}
            {!! BootForm::select('email_id', 'Select email', $user->emails->pluck('email', 'id')) !!}
            {!! BootForm::submit() !!}
        {!! BootForm::close() !!}
    @else
        User #{{$user->id}} emails not found.
    @endif
@endsection