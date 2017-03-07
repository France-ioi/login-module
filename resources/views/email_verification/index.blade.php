@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('email_verification.header')
        </div>

        @if(session('status'))
            <div class="panel-body">
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            </div>
        @endif

        <ul class="list-group">
            @foreach($emails as $email)
                <li class="list-group-item">
                    {{ $email->email }}
                    <form method="post" action="/email_verification/send" style="display: inline">
                        {{ csrf_field() }}
                        <input type="hidden" name="email" value="{{ $email->email }}"/>
                        <button class="btn btn-xs btn-primary pull-right" type="submit">@lang('email_verification.btn_send')</button>
                    </form>
                </li>
            @endforeach
        </ul>

        <div class="panel-body">
            <p>
                @lang('email_verification.hint')
            </p>
            <p>
                <a href="{{ $authorization_url }}" class="btn btn-primary">@lang('email_verification.btn_continue')</a>
            </p>
        </div>
    </div>
@endsection