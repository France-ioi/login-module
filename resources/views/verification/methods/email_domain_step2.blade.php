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
		<p>
			@lang('verification.email_domain.step2_help', ['email' => $verification->email])
			<a href="/verification/email_domain?email={!! urlencode($verification->email) !!}">@lang('verification.email_code.resend_code')</a>
		</p>        
		{!! BootForm::open(['url' => '/verification/email_domain/validate_code/'.$verification->id]) !!}
			{!! BootForm::text('code', trans('verification.email_code.code'), $code, [
				'placeholder' => trans('verification.email_code.code'),
				'prefix' => BootForm::addonIcon('key fas')
			]) !!}			

			<div class="form-group text-center">
				<button type="submit" class="btn btn-rounded btn-primary btn-centered">
					<i class="fas fa-check icon"></i> @lang('verification.email_domain.validate')
				</button>
				<a class="btn-link" href="/verification">@lang('ui.cancel')</a>
			</div>
        {!! BootForm::close() !!}
    </div>
@endsection
