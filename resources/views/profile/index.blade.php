@extends('layouts.popup')

@section('content')
    @if(count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(count($revalidation_fields) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach($revalidation_fields as $field)
                    <li>{{ trans('merging_accounts.profile_messages')[$field] }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($login_change_required)
        <div class="alert alert-warning">
            @lang('profile.login_change_required')
        </div>
    @endif

    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

<!-- move to render -->
    @if($pms_redirect)
        <div class="alert alert-info">
            @lang('profile.pms_redirect_msg')
            <a class="btn btn-block btn-primary" href="/oauth_client/preferences/pms">
                @lang('profile.pms_redirect_btn')
            </a>
        </div>
    @endif

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('profile.header')
            @if($toggle_optional_fields_allowed)
                <div class="pull-right">
                    <div class="checkbox" style="margin: 0">
                        <label>
                            <input type="checkbox" id="display_only_required_fields"/> @lang('profile.display_only_required_fields')
                        </label>
                    </div>
                </div>
            @endif
        </div>
        <div class="panel-body">
            {!! BootForm::open($form) !!}
                @if($all)
                    {!! BootForm::hidden('all', 1) !!}
                @endif
                {!! ProfileFormRenderer::render($schema) !!}
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        @lang('ui.save')
                    </button>
                    @if($cancel_url)
                        <a class="btn btn-link" href="{{ $cancel_url }}">
                            @lang('ui.close')
                        </a>
                    @endif
            {!! BootForm::close() !!}
        </div>
    </div>

    <link href="/css/bootstrap-datepicker3.css" rel="stylesheet">
    <script type="text/javascript">
        $(document).ready(function() {
            var form = {
                submit_available: true,

                setSubmitAvailable: function(v) {
                    this.submit_available = v;
                    this.el.find('[type=submit]').prop('disabled', !this.submit_available);
                },

                onSubmit: function(e) {
                    if(this.submit_available) {
                        $(document.body).append($('<div class="overlay-spinner">'));
                    } else {
                        e.preventDefault();
                    }
                },

                init: function() {
                    this.el = $('form#profile');
                    this.el.submit(this.onSubmit.bind(this));
                }
            }
            form.init();

            $('#login').on('change', function(e) {
                $('#login_change_limitations').show();
            });


            $('#picture').on('change', function(e) {
                var max_file_size = parseFloat($(this).attr('max_file_size')) || 0;
                if(!max_file_size) return;
                var allowed_size = true;
                if(e.currentTarget.files.length) {
                    var size = (e.currentTarget.files[0].size/1048576).toFixed(2);
                    if(size > max_file_size) {
                        allowed_size = false;
                    }
                }
                form.setSubmitAvailable(allowed_size);
                var error = $('#block_picture').find('span.file_size_error').toggleClass('hidden', allowed_size);
                $(this).parent().append(error);
                $(this).closest('.form-group').toggleClass('has-error', !allowed_size);
            });


            $('#birthday').datepicker({
                format: 'yyyy-mm-dd',
                endDate: new Date(),
                autoclose: true
            });

            @if($toggle_optional_fields_allowed)
                (function(el) {
                    function toggleOptionalFields() {
                        $('[required_field=0]').toggle(!el.prop('checked'));
                    }
                    el.click(toggleOptionalFields);
                    el.prop('checked', {!! $all ? 'false' : 'true' !!});
                    toggleOptionalFields(false);
                })($('#display_only_required_fields'));
            @endif




            var emails = {
                country_code: null,
                is_teacher: false,
                domains: ['aa.bb', 'vvv.ccc'],

                install: function() {
                    if(this.country_code && this.is_teacher) {
                        $('input[name=primary_email').typeahead({
                            source: this.source,
                            autoSelect: true
                        });
                        $('input[name=secondary_email').typeahead({
                            source: this.source,
                            autoSelect: true
                        });
                    } else {
                        $('input[name=primary_email').typeahead('destroy');
                        $('input[name=secondary_email').typeahead('destroy');
                    }
                },

                refresh: function() {
                    var country_code = $('select[name=country_code]').val();
                    var is_teacher = $('select[name=role]').val() == 'teacher';
                    if(this.country_code !== country_code || this.is_teacher !== is_teacher) {
                        this.country_code = country_code;
                        this.is_teacher = is_teacher;
                        this.install();
                        $.ajax({
                            url: '/official_domains',
                            data: {
                                country_code: this.country_code
                            },
                            success: function(domains) {
                                emails.domains = domains;
                            }
                        });
                    }
                },

                source: function(value, callback) {
                    if(value.indexOf('@') >= 0) {
                        return null;
                    }
                    var res = [];
                    for(var i=0; i<emails.domains.length; i++) {
                        res.push(value + '@' + emails.domains[i]);
                    }
                    callback(res);
                }
            }
            emails.refresh();


            $('#graduation_grade').change(function() {
                var grade = $(this).val();
                var year = $('#graduation_year').val();
                $('#block_graduation_year').toggle((!grade && year) || grade == '-2');
            }).trigger('change');


            function updateTeacherDomainBlock() {
                $('#block_teacher_domain_verified').toggle($('select[name=role]').val() == 'teacher');
            }
            $('input[name=teacher_domain_verified][value=yes]').prop('checked', true);
            updateTeacherDomainBlock();
            $('select[name=role]').click(updateTeacherDomainBlock);

            function updateTeacherDomainAlert() {
                $('#teacher_domain_alert').toggle($('input[name=teacher_domain_verified]:checked').val() == 'no');
            }
            updateTeacherDomainAlert();
            $('input[name=teacher_domain_verified]').click(updateTeacherDomainAlert);


            function updateHidden() {
                $('#block_ministry_of_education_fr').hide();
                $('#block_ministry_of_education').hide();
                var country_code = $('select[name=country_code]').val();
                if(country_code == 'FR') {
                    $('#block_ministry_of_education_fr').show();
                } else {
                    $('#block_ministry_of_education').show();
                }
            }
            updateHidden();
            $('select[name=country_code]').change(function() {
                updateHidden();
                emails.refresh();
            });
            $('select[name=role]').change(function() {
                emails.refresh();
            });



            if(!$('input[name=timezone]').val()) {
                var rightNow = new Date();
                var date1 = new Date(rightNow.getFullYear(), 0, 1, 0, 0, 0, 0);
                var date2 = new Date(rightNow.getFullYear(), 6, 1, 0, 0, 0, 0);
                var temp = date1.toGMTString();
                var date3 = new Date(temp.substring(0, temp.lastIndexOf(" ") - 1));
                temp = date2.toGMTString();
                var date4 = new Date(temp.substring(0, temp.lastIndexOf(" ") - 1));
                var hoursDiffStdTime = (date1 - date3) / (1000 * 60 * 60);
                var hoursDiffDaylightTime = (date2 - date4) / (1000 * 60 * 60);
                $.ajax({
                    url: '/timezone',
                    data: {
                        offset: hoursDiffStdTime,
                        dls: hoursDiffDaylightTime == hoursDiffStdTime ? 0 : 1
                    },
                    success: function(value) {
                        $('input[name=timezone]').val(value);
                    }
                });
            }

        });
    </script>
@endsection