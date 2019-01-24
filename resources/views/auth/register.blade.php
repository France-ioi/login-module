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
            <div class="panelTitle">@lang('auth.register_title')</div>
            <div class="">
                <div class="">
                {!! BootForm::horizontal(['route' => 'register', 'class' => 'centered_form', 'left_column_offset_class' => ' ', 'right_column_class' => ' ']) !!}
                    @if($login_required)
                        {!! BootForm::text('login', false, array_get($values, 'login'),
                            ['placeholder' => trans('auth.login'), 'prefix' => BootForm::addonText('Aa')]) !!}
                    @endif
                    @if($email_required)
                        {!! BootForm::text('primary_email', false, array_get($values, 'email'),
                            ['placeholder' => trans('auth.email'), 'prefix' => BootForm::addonText('Aa')]) !!}
                    @endif
                    {!! BootForm::password('password', false, ['placeholder' => trans('auth.pwd'),  'prefix' => BootForm::addonIcon('key fas')]) !!}
                    {!! BootForm::password('password_confirmation', false, ['placeholder' => trans('auth.pwd_confirm'),  'prefix' => BootForm::addonIcon('key fas')]) !!}
                    <div class="form-group">
                        <button type="submit" class="btn btn-rounded btn-wide btn-primary"><i class="fas fa-check icon"></i>@lang('auth.btn_register')</button>
                    </div>
                    <div class="form-group">
                        <a class="btn btn-danger btn-wide btn-rounded" href="{{ url('/auth') }}">
                            <i class="fas fa-times icon"></i>
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
                    var icon = $('<span class="fas fa-question-circle profile-tooltip-icon"></span>');
                    icon.tooltip({
                        title: text
                    })
                    el.parents('.form-group').append(icon);
                }
            });
        });
    </script>
@endsection
