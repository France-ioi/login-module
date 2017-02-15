@extends('layouts.app')

@section('navigation')
    <nav class="navbar navbar-default">
        <div class="container">
            <ul class="nav navbar-nav">
                <li>
                    <a href="{{ url('/admin/users') }}">Users</a>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="{{ url('/logout') }}">Logout</a>
                </li>
            </ul>
        </div>
    </nav>


    @if(session()->has('message'))
        <div class="container">
            <div class="alert alert-info">{{ session('message') }}</div>
        </div>
    @endif
@endsection