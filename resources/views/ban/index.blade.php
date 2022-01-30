@extends('layouts.popup')

@section('content')
        <div class="alert-section">
            <div class="alert alert-danger">
                <i class="fas fa-bell icon"></i>
                @lang('ban.header')
            </div>
        </div>
        <div class="panel-body">
            <p>
                @lang('ban.text', ['client_name' => $client->name])
            </p>
        </div>
@endsection