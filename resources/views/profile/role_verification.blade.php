<div id="role_verification">
    @if(!count($role_verifications))
        <div id="rv_form1" class="form-group" style="display: none">
            <strong>Status verification:</strong>
            <p>
                Please provide as email from one of proposed domains. We will then send a code to this address,
                that you can then use to provide your status. If you don't have such an address, you may try other
                <a href="/verification">verification methods</a>.
            </p>

            <div class="row">
                <div class="col-sm-4">
                    <input class="form-control" id="inp_rv_account" placeholder="Official email"/>
                </div>
                <div class="col-sm-4">
                    <div class="input-group">
                        <div class="input-group-addon">@</div>                
                        <select class="form-control" id="sel_rv_domain">
                            @foreach($official_domains as $domain)
                                <option value="{{ $domain->domain }}">{{ $domain->domain }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <button class="btn btn-primary" id="btn_rv_send_code">Verify</button>
                </div>
            </div>
        </div>
        <div id="rv_form2" class="form-group" style="display: none">
            <p>
                Please check your email and enter code here.
            </p>        
            <div class="row">
                <div class="col-sm-4"></div>
                <div class="col-sm-4">
                    <input class="form-control" id="inp_rv_code" placeholder="Enter code"/>
                </div>
                <div class="col-sm-4">
                    <button class="btn btn-primary" id="btn_rv_verify_code">Submit</button>
                </div>
            </div>
            <div class="alert alert-danger" id="rv_code_error" style="display: none">Wrong code</div>
        </div>
        <div id="rv_status" class="form-group" style="display: none">
            <strong>Status verification:</strong> verified using email address <span id="rv_email"></span>
        </div>
    @else
        <div class="form-group">
            @foreach($role_verifications as $verification)
                @if($verification->email)
                    <strong>Status verification:</strong> verified using email address {{ $verification->email }}
                @endif
            @endforeach
        </div>

    @endif
</div>

<script>
$(document).ready(function() {
    (function() {
        $('#role_verification').insertAfter($('#block_role'));

        var form1 = $('#rv_form1');
        var form2 = $('#rv_form2');
        var verified = false;

        
        // role change
        function refreshVisibility() {
            form1.toggle($(this).val() == 'teacher' && !verified);
        }
        $('select#role').on('change', refreshVisibility).trigger('change');



        function refreshAccount() {
            var email = $(this).val();
            var tmp = email.split('@');
            var account = tmp[0] || '';
            var domain = tmp[1] || '';
            if(window.official_domains.indexOf(domain) !== -1) {
                $('#sel_rv_domain').val(domain);
                $('#inp_rv_account').val(account);
            }
        }
        $('#secondary_email').change(refreshAccount).trigger('change');
        $('#primary_email').change(refreshAccount).trigger('change');


        var email;
        var csrf = $('meta[name="csrf-token"]').attr('content');        

        $('#btn_rv_send_code').click(function(e) {
            e.preventDefault();
            var account = $('#inp_rv_account').val().trim();
            if(account == '') {
                return;
            }
            form1.find('input select button').prop('disabled', true);
            email = account + '@' + $('#sel_rv_domain').val();
            $.ajax({
                dataType: 'json',
                url: '/profile_inline_verification/send_code',
                method: 'POST',
                data: {
                    email: email,
                    _token: csrf
                },
                success: function(data) {
                    if(data.success) {
                        form1.hide();
                        form2.show();
                    }
                }
            });
        })


        $('#btn_rv_verify_code').click(function(e) {
            e.preventDefault();
            $('#rs_code_error').hide();
            var code = $('#inp_rv_code').val().trim();
            if(code == '') {            
                return;
            }
            form2.find('input select button').prop('disabled', true);
            $.ajax({
                dataType: 'json',
                url: '/profile_inline_verification/verify_code',
                method: 'POST',
                data: {
                    email: email,
                    code: code,
                    _token: csrf
                },
                success: function(data) {
                    if(data.success) {
                        form2.hide();
                        $('#rv_email').text(email);
                        $('#rv_status').show();
                        var parent = sel_role.parent();
                        parent.parent().append(sel_role);
                        parent.remove();
                        verified = true;
                    } else {
                        form2.find('input select button').prop('disabled', false);
                        $('#rv_code_error').show();
                    }
                }
            });
        });

    })();
})
</script>