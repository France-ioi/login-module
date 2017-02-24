@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('badge.badge_header')</div>
        <div class="panel-body">
                {!! BootForm::open(['url' => '/badge/attach']) !!}
                    {!! BootForm::text('code', false) !!}
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            @lang('badge.btn_verify_code')
                        </button>
                        <button class="btn btn-link" id="btn_do_not_have">
                            @lang('badge.do_not_have')
                        </button>
                    </div>
                {!! BootForm::close() !!}
        </div>
    </div>


    <script type="text/javascript">
        $('#btn_do_not_have').click(function(e) {
            $(e.target).closest('form').attr('action', '/badge/do_not_have');
        })
    </script>
@endsection