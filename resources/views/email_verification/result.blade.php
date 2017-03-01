@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('email_verification.header')</div>
        <div class="panel-body">
            @if(isset($error))
                <div class="alert alert-danger">
                    @lang('email_verification.errors.'.$error)
                </div>
            @else
                <div class="alert alert-success">
                    @lang('email_verification.success')
                </div>
            @endif
        </div>
    </div>
@endsection