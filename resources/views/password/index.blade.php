@if(Session::get('success'))
    <div class="alert alert-success">
        @lang('password.success_message')
    </div>
@endif

{!! BootForm::horizontal(['url' => '/password', 'class' => 'form-inset', 'left_column_offset_class' => ' ', 'right_column_class' => ' ']) !!}
    {!! BootForm::password('password', trans('password.pwd_new'), ['prefix' => BootForm::addonIcon('key fas')]) !!}
    {!! BootForm::password('password_confirmation', trans('auth.pwd_confirm'), ['prefix' => BootForm::addonIcon('key fas')]) !!}
    <div class="form-group">
        <button type="submit" class="btn btn-rounded btn-primary pull-right">
            <i class="fas fa-check icon"></i>
            @lang('password.btn_submit')
        </button>
        @if($cancel_url)
            <a class="btn btn-link" href="{{ $cancel_url }}">
                @lang('ui.close')
            </a>
        @endif
    </div>
{!! BootForm::close() !!}