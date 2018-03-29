@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('verification.methods.email_domain')
        </div>
        <div class="panel-body">
            <p>@lang('verification.email_domain.help')</p>
            {!! BootForm::open(['url' => '/verification/email_domain']) !!}
                {!! BootForm::select('role', trans('verification.email_domain.role'), $roles) !!}
                {!! BootForm::text('account', trans('verification.email_domain.account')) !!}
                {!! BootForm::select('domain', trans('verification.email_domain.domain'), $domains) !!}
                {!! BootForm::submit(trans('ui.save')) !!}
            {!! BootForm::close() !!}
        </div>
    </div>
    <a class="btn btn-default" href="/verification">@lang('ui.cancel')</a>
@endsection