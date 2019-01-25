@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="alert-section">
            <div class="alert alert-danger">
                <i class="fas fa-bell icon"></i>
                @lang('reauthentication.header')
            </div>
        </div>
        <div class="panel-body">
            {!! BootForm::horizontal(['url' => '/reauthentication', 'method' => 'post']) !!}
                {!! BootForm::password('password', trans('reauthentication.pwd'), ['placeholder' => trans('reauthentication.pwd'),  'prefix' => BootForm::addonIcon('key fas')]) !!}
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-rounded btn-centered">
                        <i class="fas fa-check icon"></i>
                        @lang('ui.continue')
                    </button>
                    @if($cancel_url)
                        <a class="btn btn-danger btn-rounded btn-centered" href="{{ $cancel_url }}">
                            <i class="fas fa-times icon"></i>
                            @lang('ui.close')
                        </a>
                    @endif
                </div>
            {!! BootForm::close() !!}
        </div>
    </div>
@endsection