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
            <div class="pull-right">
                <div class="checkbox" style="margin: 0">
                    <label>
                        <input type="checkbox" id="display_only_required_fields"/> @lang('profile.display_only_required_fields')
                    </label>
                </div>
            </div>
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
            $('#birthday').datepicker({
                format: 'yyyy-mm-dd',
                endDate: new Date(),
                autoclose: true
            });


            (function(el) {
                function toggleOptionalFields() {
                    $('[required_field=0]').toggle(!el.prop('checked'));
                }
                el.click(toggleOptionalFields);
                el.prop('checked', {!! $all ? 'false' : 'true' !!});
                toggleOptionalFields(false);
            })($('#display_only_required_fields'));


            function updateHidden() {
                $('#box_ministry_of_education_fr').hide();
                $('#box_ministry_of_education').hide();
                var country_code = $('#country_code').val();
                if(country_code == 'FR') {
                    $('#box_ministry_of_education_fr').show();
                } else {
                    $('#box_ministry_of_education').show();
                }
            }
            $('#country_code').change(updateHidden);
            updateHidden();
            if(!$('#timezone').val()) {
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
                        $('#timezone').val(value);
                    }
                });
            }

        });
    </script>
@endsection