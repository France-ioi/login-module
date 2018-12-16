{!! BootForm::text('name', 'Name') !!}
{!! BootForm::select('badge_api_id', 'Badge API', ['' => '...'] + $badge_apis) !!}
{!! Bootform::hidden('badge_required', 0) !!}
{!! BootForm::checkbox('badge_required', 'Badge code required') !!}
{!! BootForm::text('api_url', 'Access code verification service url') !!}
{!! BootForm::text('email', 'Email') !!}
{!! Bootform::hidden('user_helper_search_exclude', 0) !!}
{!! BootForm::checkbox('user_helper_search_exclude', 'Exclude from user search if not explicitly allowed') !!}


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