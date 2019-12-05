@extends('layouts.pages.admin')

@section('navigation')
    <nav class="navbar navbar-default">
        <div class="container-admin">
            <ul class="nav navbar-nav">
                <li>
                    <a href="{{ url('/admin') }}">@lang('admin.dashboard')</a>
                </li>
                <li>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Users
                    <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ url('/admin/users') }}">Users manager</a></li>
                        <li><a href="{{ url('/admin/reset_passwords') }}">Reset passwords</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{{ route('admin.clients.index') }}">@lang('admin.clients')</a>
                </li>
                <li>
                    <a href="{{ route('admin.official_domains.index') }}">@lang('admin.official_domains')</a>
                </li>
                <li>
                    <a href="{{ route('admin.origin_instances.index') }}">@lang('admin.origin_instances')</a>
                </li>
                <li>
                    <a href="{{ url('admin/verifications/edit') }}">Verification</a>
                </li>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Misc
                    <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('admin.lti_configs.index') }}">LTI config</a></li>
                        <li><a href="{{ route('admin.badge_apis.index') }}">Badge API</a></li>
                    </ul>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
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
