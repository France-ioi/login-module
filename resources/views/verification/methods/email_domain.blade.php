@extends('layouts.popup')

@section('content')
	@if(count($errors) > 0)
		<div class="alert-section">
			@include('ui.errors')
		</div>
	@endif

    <div class="panel-body">
		<div class="sectionTitle">
			<i class="fas fa-landmark icon"></i>
            @lang('verification.methods.email_domain')
        </div>
        <p class="help-block">@lang('verification.email_domain.help')</p>
        {!! BootForm::open(['url' => '/verification/email_domain']) !!}
            {!! BootForm::select('role', trans('verification.email_domain.role'), $roles) !!}
            {!! BootForm::text('account', trans('verification.email_domain.account')) !!}
            {!! BootForm::select('domain', trans('verification.email_domain.domain'), $domains) !!}
			<div class="form-group text-center">
				<button type="submit" class="btn btn-rounded btn-primary btn-centered">
					<i class="fas fa-check icon"></i>
					@lang('ui.save')
				</button>
				<a class="btn-link" href="/verification">@lang('ui.cancel')</a>
			</div>
        {!! BootForm::close() !!}
    </div>
@endsection
