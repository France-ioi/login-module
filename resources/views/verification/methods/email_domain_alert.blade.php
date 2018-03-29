@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('verification.methods.email_domain')
        </div>
        <div class="panel-body">
            <p>@lang('verification.email_domain.'.$alert)</p>
            <a class="btn btn-default" href="/profile">@lang('verification.btn_profile')</a>
        </div>
    </div>

@endsection