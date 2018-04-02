@extends('layouts.admin')

@section('content')
    <h2>
        @if($client->exists())
            Client ID: {{ $client->id }}
        @else
            New client
        @endif
    </h2>

    {!! BootForm::open(['model' => $client, 'store' => 'admin.clients.store', 'update' => 'admin.clients.update']) !!}
        <div>
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#general" aria-controls="general" role="tab" data-toggle="tab">
                        General
                    </a>
                </li>
                <li role="presentation">
                    <a href="#user_attributes" aria-controls="user_attributes" role="tab" data-toggle="tab">
                        User attributes
                    </a>
                </li>
                <li role="presentation">
                    <a href="#verification" aria-controls="verification" role="tab" data-toggle="tab">
                        Verification
                    </a>
                </li>
                <li role="presentation">
                    <a href="#attributes_filter" aria-controls="attributes_filter" role="tab" data-toggle="tab">
                        Attributes filter
                    </a>
                </li>
                <li role="presentation">
                    <a href="#auth" aria-controls="auth" role="tab" data-toggle="tab">
                        Auth
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="general">
                    @include('admin.clients.sections.general')
                </div>
                <div role="tabpanel" class="tab-pane" id="user_attributes">
                    @include('admin.clients.sections.user_attributes')
                </div>
                <div role="tabpanel" class="tab-pane" id="verification">
                    @include('admin.clients.sections.verification')
                </div>
                <div role="tabpanel" class="tab-pane" id="attributes_filter">
                    @include('admin.clients.sections.attributes_filter')
                </div>
                <div role="tabpanel" class="tab-pane" id="auth">
                    @include('admin.clients.sections.auth')
                </div>
            </div>
        </div>
        {!! BootForm::submit() !!}
    {!! BootForm::close() !!}

@endsection
