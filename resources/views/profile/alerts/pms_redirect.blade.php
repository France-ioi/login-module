@if($pms_redirect)
    <div class="alert alert-info">
        @lang('profile.pms_redirect_msg')
        <a class="btn btn-block btn-primary" href="/oauth_client/preferences/pms">
            @lang('profile.pms_redirect_btn')
        </a>
    </div>
@endif
