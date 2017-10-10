@extends('layouts.popup')

@section('content')
    <div class="alert alert-info">
        <strong>{{ $email }}</strong> @lang('auth_connections.email_exists_message1')
        <strong>{{ $login }}</strong>@lang('auth_connections.email_exists_message2')
    </div>
    <a href="{{ route('login') }}" class="btn btn-primary">
        @lang('ui.continue')
    </a>
@endsection