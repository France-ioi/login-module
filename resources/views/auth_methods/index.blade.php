@extends('layouts.popup')

@section('content')
    @include('auth_methods.auth_connections')
    @include('auth_methods.badges')

    @if($cancel_url)
        <a class="btn btn-link" href="{{ $cancel_url }}">
            @lang('ui.close')
        </a>
    @endif
@endsection