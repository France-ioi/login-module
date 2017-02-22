@extends('layouts.popup')

@section('content')
    <div class="alert alert-info">
        <strong>{{ $email }}</strong> already exists in database, please login then add this authentication method.
    </div>
    <a href="{{ route('login') }}" class="btn btn-primary">Continue</a>
@endsection