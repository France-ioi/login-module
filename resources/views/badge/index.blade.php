@extends('layouts.popup')

@section('content')
        <div class="panel-body">
	    <div class="sectionTitle">
		<i class="fas fa-user icon"></i>
                @lang('badge.header')
            </div>
            {!! BootForm::open(['url' => '/badge/attach']) !!}
                {!! BootForm::text('code', false, null, ['placeholder' => trans('badges.code'),  'prefix' => BootForm::addonIcon('key fas')]) !!}
                <div class="form-group text-center">
		    <button type="submit" class="btn btn-centered btn-rounded btn-primary">
			<i class="fas fa-check icon"></i>
                        @lang('badge.btn_verify_code')
                    </button>
                    <button class="btn-link" id="btn_do_not_have">
                        @lang('badge.do_not_have')
                    </button>
                </div>
            {!! BootForm::close() !!}
        </div>


    <script type="text/javascript">
        $('#btn_do_not_have').click(function(e) {
            $(e.target).closest('form').attr('action', '/badge/do_not_have');
        })
    </script>
@endsection
