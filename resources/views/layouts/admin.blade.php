@extends('layouts.app')

@section('navigation')
    <nav class="navbar navbar-default">
        <div class="container">
            <ul class="nav navbar-nav">
                <li>
                    <a href="{{ url('/admin') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ url('/admin/users') }}">Users</a>
                </li>
                <li>
                    <a href="{{ route('admin.clients.index') }}">Clients</a>
                </li>
                <li>
                    <a href="{{ route('admin.official_domains.index') }}">Official domains</a>
                </li>
                <li>
                    <a href="{{ route('admin.origin_instances.index') }}">LM instances</a>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="{{ url('/logout') }}">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    @if(session('status'))
        <div class="container">
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="container">
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        </div>
    @endif
@endsection