@extends('layouts.popup')
@section('header')
    <div class="pageTitle_wrapper">
        <div class="pageTitle">@lang('auth.pwd_reset_header')</div>
        <div class="subtitle">@lang('auth.pwd_reset_intro')</div>
    </div>
@endsection
@section('content')
        <div class="panel-heading">
            <a class="back_link" href="{{ url('auth') }}">
                <i class="fas fa-arrow-left"></i>
                @lang('auth.select_another_method')
            </a>
        </div>
        <div class="panel-body">
            <div class="panelTitle">@lang('auth.emails_list_header')</div>
            <div class="row">
                <div class="col-sm-6 col-centered">
                    @foreach($emails as $email)
                        {!! BootForm::open(['route' => 'password.email', 'class' => 'reset-psw']) !!}
                            {!! BootForm::hidden('email_id', $email->id) !!}
                            <a href="#" role="submit">{{ EmailMasker::mask($email->email) }}</a>
                        {!! BootForm::close() !!}
                    @endforeach
                </div>
            </div>
        </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $('a[role=submit]').each(function(i, el) {
                $(el).click(function() {
                    $(el).closest('form').submit();
                })
            })
        })
    </script>
@endsection
