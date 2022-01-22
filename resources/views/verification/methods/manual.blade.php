@extends('layouts.popup')

@section('content')
    <div class="panel-body">
        <div class="sectionTitle">
            <i class="fas fa-envelope icon"></i>
            @lang('verification.methods.manual')
        </div>            
        <p>@lang('verification.manual.help', [
            'client_email' => $client && $client->email ? $client->email : config('verification.default_client_email')
        ])</p>
        
        <div class="form-group text-center">
            <a class="btn-link" href="/verification">@lang('ui.cancel')</a>
        </div>            
    </div>
@endsection