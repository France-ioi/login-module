@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('verification.methods.email_code')
        </div>
        <div class="panel-body">
            @if(count($emails))
                <p>
                    @lang('verification.email_code.help', [
                        'email' => '<a href="mailto:'.config('mail.from.address').'">'.config('mail.from.address').'</a>'
                    ])
                </p>
                {!! BootForm::open(['url' => '/verification/email_code']) !!}
                    {!! BootForm::select('role', trans('verification.email_code.email'), $emails) !!}
                    {!! BootForm::text('code', trans('verification.email_code.code')) !!}
                    {!! BootForm::submit(trans('ui.save')) !!}
                {!! BootForm::close() !!}
            @else
                <div class="alert alert-warning">@lang('verification.email_code.no_emails')</div>
            @endif
        </div>
    </div>
    <a class="btn btn-default" href="/verification">@lang('ui.close')</a>
@endsection