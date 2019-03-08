@extends('layouts.popup')

@section('content')
        <div class="panel-body">
            <div class="sectionTitle">
                <i class="fas fa-qrcode icon"></i>
                @lang('badges.header')
            </div>
            @include('ui.status')
            @if(!count($available))
                <div class="alert alert-success">@lang('badges.nothing_to_add')</div>
            @else
                @include('badges.form')
            @endif
        </div>
@endsection