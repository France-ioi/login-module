@extends('layouts.pages.admin')

@section('navigation')
    <nav class="navbar navbar-default">
        <div class="container-admin">
            <ul class="nav navbar-nav">
                <li>
                    <a href="{{ route('client_admin.users', $client->id) }}">{{ $client->name }} &raquo; Users manager</a>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="{{ url('/profile') }}">@lang('profile.header')</a>
                </li>                
                <li>
                    <a href="{{ url('/logout') }}">@lang('auth.logout')</a>
                </li>
            </ul>            
        </div>
    </nav>
    @if(session('status'))
        <div class="container-admin">
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="container-admin">
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        </div>
    @endif


    <script type="text/javascript">
        $(document).ready(function() {
            $("form[role=delete]").submit(function(e) {
                if(!confirm('Are you sure you want to delete this record?')) {
                    e.preventDefault();
                    return false;
                }
            });
        })
    </script>
@endsection
