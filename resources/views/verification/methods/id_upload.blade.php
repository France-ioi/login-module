@extends('layouts.popup')

@section('content')
    @include('ui.errors')

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('verification.methods.id_upload')
        </div>
        <div class="panel-body">
            <p>@lang('verification.id_upload.help')</p>
            {!! BootForm::open(['url' => '/verification/id_upload', 'files' => true]) !!}
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

                <label><p>@lang('verification.id_upload.list')</p></label>
                @foreach($method->user_attributes as $attribute)
                    {!! BootForm::checkbox('user_attributes[]', trans('profile.'.$attribute), $attribute) !!}
                @endforeach
                {!! BootForm::submit(trans('ui.save')) !!}
            {!! BootForm::close() !!}
        </div>
    </div>
    <a class="btn btn-default" href="/verification">@lang('ui.close')</a>

@endsection