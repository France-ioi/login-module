<div class="panel panel-default">
    <div class="panel-heading">
        @lang('auth.change_pwd_header')
    </div>
    <div class="panel-body">
        {!! BootForm::open(['route' => 'update_password', 'method' => 'post']) !!}
            <input type="hidden" name="redirect_uri" value="{{ $redirect_uri }}"?>
            {!! BootForm::password('password', trans('auth.pwd_new')) !!}
            {!! BootForm::password('password_confirmation', trans('auth.pwd_confirm')) !!}
            {!! BootForm::submit(trans('auth.btn_pwd_change')) !!}
        {!! BootForm::close() !!}
    </div>
</div>