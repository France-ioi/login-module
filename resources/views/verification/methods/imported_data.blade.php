@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('verification.methods.imported_data')
        </div>
        <div class="panel-body">
            <p>@lang('verification.imported_data')</p>
        </div>
    </div>
    <a class="btn btn-default" href="/verification">@lang('ui.close')</a>
@endsection