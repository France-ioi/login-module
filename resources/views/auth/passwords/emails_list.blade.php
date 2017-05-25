@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('auth.emails_list_header')</div>
        <div class="panel-body">
            @foreach($emails as $email)
                {!! BootForm::open(['route' => 'password.email']) !!}
                    {!! BootForm::hidden('email_id', $email->id) !!}            
                    <a href="#" role="submit">{{ EmailMasker::mask($email->email) }}</a>
                {!! BootForm::close() !!}
            @endforeach
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
