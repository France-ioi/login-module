@extends('layouts.popup')

@section('aside')
    @include('layouts.components.left_menu')
@endsection

@section('content')
    <div class="alert-section">
        @include('ui.status')
        @include('ui.errors')
        
        @if($login_fixed)
            <div class="alert alert-danger">
                @lang('profile.login_fixed')
            </div>
        @elseif($login_change_restricted)
            <div class="alert alert-warning">
                @lang('profile.login_change_restricted', ['date' => $restriction_end_date->format('d/m/Y')])
            </div>
        @endif
    </div>
    
    <div class="info-message">
        @lang('profile.username_change_explanation')
    </div>
    
    <div class="panel-body">
        {!! BootForm::horizontal([
            'url' => '/profile/username',
            'method' => 'post',
            'class' => 'usernameChangeForm form-horizontal'
        ]) !!}
            
            <div class="form-group">
                <label class="col-sm-3 control-label">@lang('profile.current_login')</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><strong>{{ $user->login }}</strong></p>
                </div>
            </div>
            
            {!! BootForm::text(
                'login',
                trans('profile.new_login'),
                null,
                ($login_fixed || $login_change_restricted) ? ['disabled'] : []
            ) !!}
            
            @if($suggested_login)
                <div class="form-group" id="suggested_login_msg">
                    <div class="col-sm-9 col-sm-offset-3">
                        <div class="alert alert-info">
                            <span></span>
                            <a href="javascript:void(0)" class="btn btn-sm btn-info">
                                @lang('ui.yes')
                            </a>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="form-group" id="login_change_limitations" style="display: none;">
                <div class="col-sm-9 col-sm-offset-3">
                    <div class="alert alert-info">
                        @lang('profile.login_change_limitations')
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-sm-9 col-sm-offset-3">
                    <button type="submit" class="btn btn-primary btn-rounded" {{ ($login_fixed || $login_change_restricted) ? 'disabled' : '' }}>
                        <i class="fas fa-check icon"></i>
                        @lang('profile.change_username')
                    </button>
                    <a class="btn btn-link" href="/profile">
                        @lang('ui.cancel')
                    </a>
                </div>
            </div>
        {!! BootForm::close() !!}
    </div>

    @if($suggested_login)
        <div id="suggested_login_text" style="display: none">
            @lang('auth.suggested_login', [
                'login' => '<strong>'.$suggested_login.'</strong>'
            ])
        </div>
    @endif

    <div id="login_protected_warning_text" style="display: none;">
        @lang('profile.login_protected_warning')
    </div>
    
    <script type="text/javascript">
        var login_validator = {!! json_encode($login_validator) !!};
    </script>
    
    <script type="text/javascript">
        $(document).ready(function() {
            if($('#suggested_login_text').length) {
                var el = $('#suggested_login_msg');
                el.find('span').html($('#suggested_login_text').html());
                el.find('a').click(function() {
                    $('#login').val($('#suggested_login_text').find('strong').text());
                    el.remove();
                });
                el.show();
            }
        });
    </script>
@endsection
