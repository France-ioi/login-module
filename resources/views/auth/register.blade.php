@extends('layouts.popup')

@section('content')

    <div class="pageTitle_wrapper">
        <div class="pageTitle">@lang('auth.register_header')</div>
        <div class="subtitle">@lang('auth.register_intro', ['platform_name' => $platform_name])</div>
    </div>
    <div class="panel panel-auth">
        <div class="panel-heading">
            <a class="back_link" href="{{ url('auth') }}">
                <i class="fas fa-arrow-left"></i>@lang('auth.select_another_method')
            </a>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6 col-centered">
                {!! BootForm::horizontal(['route' => 'register', 'left_column_offset_class' => ' ', 'right_column_class' => ' ']) !!}
                    @if($login_required)
                        {!! BootForm::text('login', false, array_get($values, 'login'),
                            ['placeholder' => trans('auth.login')]) !!}
                    @endif
                    @if($email_required)
                        {!! BootForm::text('primary_email', false, array_get($values, 'email'),
                            ['placeholder' => trans('auth.email')]) !!}
                    @endif
                    {!! BootForm::password('password', false, ['placeholder' => trans('auth.pwd')]) !!}
                    {!! BootForm::password('password_confirmation', false, ['placeholder' => trans('auth.pwd_confirm')]) !!}
                    {!! BootForm::submit(trans('auth.btn_register'), ['class' => 'btn btn-rounded btn-wide btn-primary']) !!}
                    <div class="form-group">
                        <a class="btn btn-danger btn-wide btn-rounded" href="{{ url('/auth') }}">
                            <i class="fas fa-times"></i>
                            @lang('ui.cancel')
                        </a>
                    </div>
                {!! BootForm::close() !!}
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var tooltips = {!! json_encode(trans('profile.tooltips')) !!};
            $('form').find('input').each(function() {
                var el = $(this);
                var text = tooltips[el.attr('name')];
                if (text) {
                    var icon = $('<span class="glyphicon glyphicon-question-sign profile-tooltip-icon"></span>');
                    icon.tooltip({
                        title: text
                    })
                    el.parents('.form-group').addClass('relativeP').append(icon);
                }
            });
        });
    </script>
@endsection
