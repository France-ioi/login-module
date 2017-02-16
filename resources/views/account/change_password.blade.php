<div class="panel panel-default">
    <div class="panel-heading">
        @lang('auth.change_pwd_header')
    </div>
    <div class="panel-body">
        <form class="form-horizontal" role="form" method="POST" action="{{ route('update_password') }}">
            {{ csrf_field() }}
            <input type="hidden" name="redirect_uri" value="{{ $redirect_uri }}"?>

            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="old_password" class="col-md-4 control-label">@lang('auth.pwd_new')</label>
                <div class="col-md-6">
                    <input id="password" type="password" class="form-control" name="password">
                    @if ($errors->has('password'))
                        <span class="help-block">{{ $errors->first('password') }}</span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                <label for="password_confirmation" class="col-md-4 control-label">@lang('auth.pwd_confirm')</label>
                <div class="col-md-6">
                    <input id="password_confirmation" type="password" class="form-control" name="password_confirmation">
                    @if ($errors->has('password_confirmation'))
                        <span class="help-block">{{ $errors->first('password_confirmation') }}</span>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-6 col-md-offset-4">
                    <button type="submit" class="btn btn-primary">
                        @lang('auth.btn_pwd_change')
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>