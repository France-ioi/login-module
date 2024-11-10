@extends('layouts.popup')

@section('header')
@endsection

@section('content')
    <div class="panel-body">
        <div class="sectionTitle">
            <i class="fas fa-key icon"></i>
            @lang('group_admin.password_change.title')
        </div>
        @if(session('status'))
            <div class="alert {{ session('status') == 'done' ? 'alert-success' : 'alert-danger' }}">
                @lang('group_admin.password_change.status.' . session('status'))
            </div>
        @endif
        <p><b>@lang('group_admin.password_change.subtitle') {{ $user->login }}</b></p>

        {!! BootForm::horizontal() !!}
            {!! BootForm::password('password', trans('password.pwd_new'), ['prefix' => BootForm::addonIcon('key fas')]) !!}
            {!! BootForm::password('password_confirmation', trans('auth.pwd_confirm'), ['prefix' => BootForm::addonIcon('key fas')]) !!}
            <div class="form-group">
                <button type="submit" class="btn btn-rounded btn-primary btn-centered">
                    <i class="fas fa-check icon"></i> @lang('ui.continue')
                </button>
                <a type="button" class="btn btn-rounded btn-default btn-centered" href="{{ $return_url }}">
                    <i class="fas fa-times icon"></i> @lang('ui.cancel')
                </a>
            </div>
        {!! BootForm::close() !!}
        </div>
@endsection
