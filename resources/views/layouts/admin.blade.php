@extends('layouts.app')

@section('navigation')
    <nav class="navbar navbar-default">
        <div class="container">
            <ul class="nav navbar-nav">
                @role('admin')
                    <li>
                        <a href="{{ url('/admin') }}">@lang('admin.dashboard')</a>
                    </li>
                @endrole
                @can('admin.user_helper')
                    <li>
                        <a href="{{ url('/admin/user_helper') }}">User helper</a>
                    </li>
                @endcan
                @can('admin.users.manager')
                    <li>
                        <a href="{{ url('/admin/users') }}">@lang('admin.users')</a>
                    </li>
                @endcan
                @can('admin.clients.manager')
                    <li>
                        <a href="{{ route('admin.clients.index') }}">@lang('admin.clients')</a>
                    </li>
                @endcan
                @can('admin.domains.manager')
                    <li>
                        <a href="{{ route('admin.official_domains.index') }}">@lang('admin.official_domains')</a>
                    </li>
                @endcan
                @can('admin.lm_instances.manager')
                    <li>
                        <a href="{{ route('admin.origin_instances.index') }}">@lang('admin.origin_instances')</a>
                    </li>
                @endcan
                @can('admin.verifications.verify')
                    <li>
                        <a href="{{ url('admin/verifications/edit') }}">Verification</a>
                    </li>
                @endcan
                @can('admin.misc')
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Misc
                        <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('admin.lti_configs.index') }}">LTI config</a></li>
                            <li><a href="{{ route('admin.badge_apis.index') }}">Badge API</a></li>
                            <li><a href="{{ url('admin/user_helper_log') }}">User helper log</a></li>
                        </ul>
                    </li>
                @endcan
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="{{ url('/account') }}">My account</a>
                </li>
                <li>
                    <a href="{{ url('/logout') }}">@lang('auth.logout')</a>
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
