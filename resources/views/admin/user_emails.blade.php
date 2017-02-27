@extends('layouts.admin')

@section('content')
    @if(count($user->emails))
        {!! BootForm::open(['url' => '/admin/users/send_reset_link']) !!}
            {!! BootForm::select('email', 'User #'.$user->id.' emails', $user->emails->pluck('email', 'email')) !!}
            {!! BootForm::submit() !!}
        {!! BootForm::close() !!}
    @else
        User #{{$user->id}} emails not found.
    @endif
@endsection