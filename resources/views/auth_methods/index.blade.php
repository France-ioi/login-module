@extends('layouts.popup')

@section('content')
        @if($display_alert)
            <div class="alert-section">
                <div class="alert alert-info">
                    @lang('auth_methods.alert')
                </div>
            </div>
        @endif
        <div class="panel-body">
            <div class="sectionTitle">
                <i class="fas fa-unlock-alt icon"></i>
                @lang('auth_methods.title')
            </div>
            <div class="panel-content">
                <ul class="list-group data-table">
                    @include('auth_methods.password')
                    @include('auth_methods.auth_connections')
                    @include('auth_methods.badges')
                </ul>
            </div>
        </div>

    @if($cancel_url)
        <a class="btn btn-link" href="{{ $cancel_url }}">
            @lang('ui.close')
        </a>
    @endif
@endsection