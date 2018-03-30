@extends('layouts.admin')

@section('content')
    <a class="btn btn-default" href="/admin/users/{{ Auth::user()->id }}/password">Change my password</a>
@endsection