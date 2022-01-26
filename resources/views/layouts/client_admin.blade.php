@extends('layouts.pages.admin')

@section('navigation')
    <nav class="navbar navbar-default">
        <div class="container-admin">
            <ul class="nav navbar-nav">
                <li>
                    <a href="/client_admin/{{ $client->id }}/users">{{ $client->name }} &raquo; Users manager</a>
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
