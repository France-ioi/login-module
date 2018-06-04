{!! BootForm::text('name', 'Name') !!}
{!! Bootform::hidden('revoked', 0) !!}
{!! BootForm::checkbox('revoked', 'Revoked') !!}
{!! BootForm::text('secret', 'Secret') !!}
{!! BootForm::text('redirect', 'Redirect') !!}
{!! BootForm::text('badge_url', 'Badge URL') !!}
{!! Bootform::hidden('badge_required', 0) !!}
{!! BootForm::checkbox('badge_required', 'Badge code required.') !!}
{!! Bootform::hidden('badge_autologin', 0) !!}
{!! BootForm::checkbox('badge_autologin', 'Login with a badge code, without asking for a login and password.') !!}
{!! BootForm::text('api_url', 'Access code verification service url') !!}
{!! BootForm::text('email', 'Email') !!}