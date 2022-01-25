<h3>Attributes that must be verified for every user</h3>
<div class="row">
    @foreach($verifiable_attributes as $attr)
        <div class="col-sm-4 col-xs-6">
            {!! BootForm::checkbox(
                'verifiable_attributes[]',
                $attr,
                $attr,
                in_array($attr, $client->verifiable_attributes)
            )!!}
        </div>
    @endforeach
</div>


<h3>Verification methods</h3>
<table class="table">
    <tr>
        <th class="col-md-5">Verification method</th>
        <th class="col-md-3">Attributes</th>
        <th class="col-md-2">Accepted</th>
        <th class="col-md-2">Recommended</th>
        <th class="col-md-2">Expiration (days)</th>
    </tr>
    @foreach($verification_methods as $method)
        <tr>
            <td>
                {{ trans('verification.methods.'.$method->name) }}
            </td>
            <td>
                {{ implode(', ', $method->user_attributes) }}
            </td>
            <td>
                {!! BootForm::checkbox(
                    'verification_methods[]',
                    false,
                    $method->id,
                    isset($client_verification_methods[$method->id])
                )!!}
            </td>
            <td>
                {!! BootForm::checkbox(
                    'verification_methods_recommended['.$method->id.']',
                    false,
                    $method->id,
                    isset($client_verification_methods[$method->id]) ? $client_verification_methods[$method->id]->recommended : false
                )!!}
            </td>            
            <td>
                {!! BootForm::text(
                    'verification_methods_expiration['.$method->id.']',
                    false,
                    isset($client_verification_methods[$method->id]) ? $client_verification_methods[$method->id]->expiration : ''
                ) !!}
            </td>
        </tr>
    @endforeach
</table>


<script type="text/javascript">
    $(document).ready(function() {
        function refreshVerificationMethod() {
            var id = $(this).val();
            $('input[name="verification_methods_expiration[' + id + ']').toggle($(this).prop('checked'));
        }
        $('input[name="verification_methods[]"]').change(refreshVerificationMethod).trigger('change');
    })
</script>