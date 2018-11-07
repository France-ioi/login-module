@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('badges.header')
        </div>
        <div class="panel-body">
            @include('ui.status')
            @if(!count($available))
                <div class="alert alert-success">@lang('badges.nothing_to_add')</div>
            @else
                @include('badges.form')
            @endif
        </div>
    </div>

    <a class="btn btn-default" href="/account">@lang('ui.cancel')</a>
@endsection