@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('password.header')
        </div>
        <div class="panel-body">
            @if(Session::get('success'))
                <div class="alert alert-success">
                    @lang('password.success_message')
                </div>
            @endif

            {!! BootForm::open(['url' => '/password']) !!}
                {!! BootForm::password('password', trans('password.pwd_new')) !!}
                {!! BootForm::password('password_confirmation', trans('auth.pwd_confirm')) !!}
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        @lang('password.btn_submit')
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