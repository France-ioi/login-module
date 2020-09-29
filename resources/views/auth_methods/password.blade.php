<li class="list-group-item">
    {{ trans('auth_methods.password') }}
    @if($has_password)
        <span class="label label-success">
            <i class="fas fa-check"></i>
            @lang('auth_methods.active')
        </span>
    @endif
    <button type="button" class="btn-link pull-right" data-toggle="collapse" data-target="#edit-password" aria-expanded>
        <span class="openCollapseItem"><i class="fas fa-pencil-alt icon"></i> @lang($has_password ? 'auth_methods.btn_change' : 'auth_methods.btn_add')</span>
        <span class="closeCollapseItem"><i class="fas fa-times icon"></i> @lang('ui.cancel')</span>
    </button>
	<div class="collapse" id="edit-password">
	    <div class="inline-form">
	        @include('auth_methods.password_form')
	    </div>
	</div>
</li>

@if($errors->has('password') || $errors->has('password_confirmation'))
    <script type="text/javascript">
        $('[data-target="#edit-password"]').attr('aria-expanded', true);
        $('#edit-password').addClass('in').attr('aria-expanded', true);
    </script>
@endif

<script type="text/javascript">
    (function() {
        var show_password_form = {!! request()->get('show_password_form') ? 'true' : 'false' !!}
        if(show_password_form) {
            $('#edit-password').collapse('show');
        }
    })();
</script>