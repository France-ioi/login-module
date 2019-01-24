@extends('layouts.popup')

@section('content')

    <div class="panel panel-default">
        <div class="panel-body">
            @if($schema->hasRequiredAttributes())
                <div class="pull-right">
                    <div class="checkbox" style="margin: 0">
                        <label>
                            <input type="checkbox" id="optional_fields_filter"/> @lang('profile.optional_fields_filter')
                        </label>
                    </div>
                </div>
            @endif
            @include('profile.alerts.filter')
            @include('profile.alerts.verification')
            @include('profile.alerts.revalidation')
            @include('profile.alerts.login_change_required')
            @include('profile.alerts.pms_redirect')
            @include('ui.status')
            @include('ui.errors')
            <div class="alert alert-info">
                @lang('profile.required_fields_explanation')
                <a class="btn btn-primary btn-xs pull-right" href="/collected_data">@lang('profile.collected_data')</a>
            </div>
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
                    //this.el.submit(this.onSubmit.bind(this));
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
                autoclose: true,
                language: '{!! app()->getLocale() !!}'
            });






            var emails = {
                country_code: null,
                is_teacher: false,
                domains: [],

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



            (function(el) {
                function toggleOptionalFields() {
                    var visible = !el.prop('checked');
                    $('#profile div[optional_field=1]').toggle(visible);
                    $('#profile fieldset').each(function(idx, el) {
                        el = $(el);
                        if(visible) {
                            el.show();
                        } else {
                            var has_visible_blocks = el.find('div[role=block]:visible').length > 0;
                            el.toggle(has_visible_blocks);
                        }
                    });
                }
                el.click(toggleOptionalFields);
                el.prop('checked', {!! $all ? 'false' : 'true' !!});
                toggleOptionalFields();
                $('#graduation_grade').trigger('change');
            })($('#optional_fields_filter'));



            $('#graduation_grade').change(function() {
                var grade = $(this).val();
                var year = $('#graduation_year').val();
                var year_visible = (!grade && year) || grade == '-2';
                var grade_visible = $('#graduation_grade').is(':visible');
                if(year_visible && grade_visible) {
                    $('#block_graduation_year').show();
                } else {
                    $('#graduation_year').val('');
                    $('#block_graduation_year').hide();
                }
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



            (function() {

                var verified_attributes = {!! json_encode($verified_attributes) !!}
                var initial_values = {};
                for(var i=0, attr; attr=verified_attributes[i]; i++) {
                    initial_values[attr] = $('#' + attr).val();
                }

                function changedAttributes() {
                    var res = [];
                    for(var i=0, attr; attr=verified_attributes[i]; i++) {
                        if(initial_values[attr] && $('#' + attr).val() != initial_values[attr]) {
                            res.push(attr)
                        }
                    }
                    return res;
                }

                function showAlert(attributes) {
                    $('#verification_alert li').hide();
                    for(var i=0, attr; attr=attributes[i]; i++) {
                        $('#verification_alert #verification_alert_' + attr).show();
                    }
                    $('#verification_alert').modal('show');
                }

                function onSubmit(e) {
                    var attributes = changedAttributes();
                    if(attributes.length) {
                        e.preventDefault();
                        $('#verification_alert_save').on('click', function() {
                            $('#profile').off('submit', onSubmit);
                            $('#profile').submit();
                        });
                        showAlert(attributes);
                    } else {
                        form.onSubmit(e);
                    }
                }
                $('#profile').on('submit', onSubmit);
            })()

            var tooltips = {!! json_encode(trans('profile.tooltips')) !!}
            $('form#profile').find('label').each(function() {
                var label = $(this)
                var text = tooltips[label.attr('for')];
                if(text) {
                    var icon = $('<span class="glyphicon glyphicon-question-sign profile-tooltip-icon"></span>');
                    icon.tooltip({
                        title: text
                    })
                    label.append(icon);
                }
            });

        });
    </script>



    <div class="modal fade" tabindex="-1" role="dialog" id="verification_alert">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('ui.alert')</h4>
                </div>
                <div class="modal-body">
                    <p>@lang('profile.verification_alert_p1')</p>
                    <ul>
                        @foreach($verified_attributes as $attr)
                            <li id="verification_alert_{{$attr}}">@lang('profile.'.$attr)</li>
                        @endforeach
                    </ul>
                    <p></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('ui.cancel')</button>
                    <button type="button" class="btn btn-primary" id="verification_alert_save">@lang('ui.save')</button>
                </div>
            </div>
        </div>
    </div>

@endsection