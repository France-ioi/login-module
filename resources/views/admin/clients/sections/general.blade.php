{!! BootForm::text('name', 'Name') !!}
{!! Bootform::hidden('revoked', 0) !!}
{!! BootForm::checkbox('revoked', 'Revoked') !!}
{!! BootForm::text('secret', 'Secret') !!}
{!! BootForm::text('redirect', 'Redirect') !!}
{!! BootForm::select('badge_api_id', 'Badge API', ['' => '...'] + $badge_apis) !!}
{!! Bootform::hidden('badge_required', 0) !!}
{!! BootForm::checkbox('badge_required', 'Badge code required.') !!}
{!! BootForm::text('api_url', 'Access code verification service url') !!}
{!! BootForm::text('admin_interface_url', 'Users admin interface url') !!}
{!! BootForm::text('email', 'Email') !!}


<script type="text/javascript">
    $(document).ready(function() {
        function onBagdeApiChange() {
            var badge_api_id = $("select[name=badge_api_id]").val();
            var el = $("input[name=badge_required]");
            if(badge_api_id) {
                el.closest('.form-group').show();
            } else {
                el.prop('checked', false);
                el.closest('.form-group').hide();
            }
        }
        $("select[name=badge_api_id]").on('change', onBagdeApiChange);
        onBagdeApiChange();
    })
</script>