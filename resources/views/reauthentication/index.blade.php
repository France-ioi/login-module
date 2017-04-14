@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('reauthentication.header')
        </div>
        <div class="panel-body">
            {!! BootForm::open(['url' => '/reauthentication', 'method' => 'post']) !!}
                {!! BootForm::password('password', trans('reauthentication.pwd')) !!}
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        @lang('ui.continue')
                    </button>
                    @if($cancel_url)
                        <a class="btn btn-link" href="{{ $cancel_url }}">
                            @lang('ui.close')
                        </a>
                    @endif
                </div>
            {!! BootForm::close() !!}
        </div>
    </div>
@endsection