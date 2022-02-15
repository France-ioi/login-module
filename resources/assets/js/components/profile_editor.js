module.exports = function(options) {

    if($('#suggested_login_text').length) {
        var el = $('#suggested_login_msg');
        el.find('span').text($('#suggested_login_text').text());
        el.find('a').click(function() {
            $('#login').val($('#suggested_login_text').find('strong').text());
            el.remove();
        });
        el.show();
    }


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



    // login
    var sanitiser = require('./login_sanitiser.js')(options.login_validator);
    function sanitizeLogin() {
        var str = $('#login').val();
        str = sanitiser.sanitise(str);
        $('#login').val(str);
    }
    $('#login').on('keyup', sanitizeLogin);
    $('#login').on('mouseup', sanitizeLogin);


    $('#login').on('change', function(e) {
        $('#login_change_limitations').show();
        refreshPublicInfo();
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
        language: options.app_locale
    });






    var emails = {
        country_code: null,
        is_teacher: false,

        install: function() {
            if(this.is_teacher) {
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
            var is_teacher = $('select[name=role]').val() == 'teacher';
            if(this.is_teacher !== is_teacher) {
                this.is_teacher = is_teacher;
                this.install();
            }
        },

        source: function(value, callback) {
            if(value.indexOf('@') >= 0) {
                return null;
            }
            var res = [];
            for(var i=0; i<options.official_domains.length; i++) {
                res.push(value + '@' + options.official_domains[i]);
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
        el.prop('checked', !options.optional_fields_visible);
        toggleOptionalFields();
        $('#graduation_grade').trigger('change');
    })($('#optional_fields_filter'));



    function refreshPublicInfo() {
        $('#public_info_login').html($('#login').val());
        var grade = $('#graduation_grade').val();
        if(grade == '-2') {
            var grade_info = $('#graduation_year').val()
        } else {
            var grade_info = $('#graduation_grade').find('option:selected').text();
        }
        $('#public_info_grade').html(grade_info);
    }


    $('#graduation_year').change(function() {
        refreshPublicInfo();
    });


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
        refreshPublicInfo();
    }).trigger('change');


    function refreshPublicName() {
        $('#public_name_first_name').html($('#first_name').val());
        $('#public_name_last_name').html($('#last_name').val());
    }
    $('#first_name').change(refreshPublicName);
    $('#last_name').change(refreshPublicName).trigger('change');


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

        var initial_values = {};
        for(var i=0, attr; attr=options.verified_attributes[i]; i++) {
            initial_values[attr] = $('#' + attr).val();
        }

        function changedAttributes() {
            var res = [];
            for(var i=0, attr; attr=options.verified_attributes[i]; i++) {
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


    $('form#profile').find('label').each(function() {
        var label = $(this)
        var text = options.tooltips[label.attr('for')];
        if(text) {
            var icon = $('<span class="fas fa-question-circle profile-tooltip-icon"></span>');
            icon.tooltip({
                title: text,
                placement: 'left'
            })
            label.parents('.form-group').append(icon);
        }
    });

}