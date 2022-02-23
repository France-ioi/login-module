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
		<p>@lang('verification.email_domain.step1_help')</p>
		{!! BootForm::open(['url' => '/verification/email_domain']) !!}
			<div class="row">
				<div class="col-sm-6">
					{!! BootForm::text('account', trans('verification.email_domain.account'), $email['account']) !!}			
				</div>
				<div class="col-sm-6">
					{!! BootForm::select(
						'domain', 
						trans('verification.email_domain.domain'), 
						['' => ''] + $official_domains, 
						$email['domain'], 
						[
							'prefix' => BootForm::addonText('@')
						]
					) !!}
				</div>
			</div>
			<div class="form-group text-center">
				<button type="submit" class="btn btn-rounded btn-primary btn-centered">
					<i class="fas fa-check icon"></i> @lang('verification.email_code.send_code')
				</button>
				<a class="btn-link" href="/verification">@lang('ui.cancel')</a>
			</div>
        {!! BootForm::close() !!}
    </div>
@endsection
