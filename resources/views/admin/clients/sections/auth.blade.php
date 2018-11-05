<h3>Login page auth methods setup (drag&drop to reorder)</h3>
<div>
    <ul class="list-group list-group-sortable" id="auth-order">
        @foreach($auth_methods as $method)
            <li class="list-group-item">
                <input type="hidden" name="auth_order[]" value="{{ $method }}"/>
                @if($method == '_HIDDEN')
                    <strong>Hidden by default methods</strong>
                @elseif($method == '_DISABLED')
                    <strong>Always hidden methods</strong>
                @else
                    @lang('auth_methods.titles.'.$method)
                @endif
            </li>
        @endforeach
    </ul>
</div>


<h3>Auth config</h3>
{!! Bootform::hidden('autoapprove_authorization', 0) !!}
{!! BootForm::checkbox('autoapprove_authorization', 'Auto-approve authorizations to this platform') !!}
{!! BootForm::textArea('public_key', 'Public key (LTI)') !!}

<style type="text/css">
    .list-group-sortable li {
        cursor: move;
    }
</style>

<script type="text/javascript">
    $(document).ready(function() {
        $('#auth-order').sortable();
    })
</script>