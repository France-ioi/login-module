@if($form['model']->login_change_required)
    <div class="alert alert-warning">
        @lang('profile.login_change_required')
    </div>
@endif