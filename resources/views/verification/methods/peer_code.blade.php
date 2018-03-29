@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('verification.methods.peer')
        </div>
        <div class="panel-body">
            <p>@lang('verification.peer.code_help')</p>
            {!! BootForm::open(['url' => '/verification/peer_code/'.$verification->id]) !!}
                {!! BootForm::text('code', trans('verification.peer.code')) !!}
                {!! BootForm::submit(trans('ui.save')) !!}
            {!! BootForm::close() !!}
        </div>
    </div>
    <a class="btn btn-default" href="/verification">@lang('ui.close')</a>

@endsection