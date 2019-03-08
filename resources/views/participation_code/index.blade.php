@extends('layouts.popup')

@section('content')
        <div class="panel-body">
            <p>@lang('participation_code.p1')</p>
            <h2>{{ $participation_code }}</h2>
            <p>@lang('participation_code.p2')</p>
            <a class="btn btn-default" href="{{ $url }}">@lang('ui.continue')</a>
        </div>
@endsection
