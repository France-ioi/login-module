@extends('layouts.popup')

@section('content')

    @include('ui.errors')

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('verification.methods.classroom_upload')
        </div>
        <div class="panel-body">
            <p>@lang('verification.classroom_upload.help')</p>
            {!! BootForm::open(['url' => '/verification/classroom_upload', 'files' => true]) !!}
                <div class="well text-center">
                    <strong>Code</strong>
                    <h2>{{$code}}</h2>
                </div>
                {!! BootForm::file(
                    'file',
                    trans('verification.upload.file'),
                    [
                        'accept' => '.gif,.jpg,.png',
                        'max_file_size' => $max_file_size
                    ]
                )!!}
                <span class="help-block">
                    @lang('verification.upload.file_size', ['size' => $max_file_size])
                </span>
                {!! BootForm::submit(trans('ui.save')) !!}
            {!! BootForm::close() !!}
        </div>
    </div>
    <a class="btn btn-default" href="/verification">@lang('ui.close')</a>

@endsection