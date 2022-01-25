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
			Please check your email <a href="mailto:{{ $verification->email }}">{{ $verification->email }}</a> and enter code here.
		</p>        
		{!! BootForm::open(['url' => '/verification/email_domain/validate_code/'.$verification->id]) !!}
			{!! BootForm::text('code', 'Code') !!}

			<div class="form-group text-center">
				<button type="submit" class="btn btn-rounded btn-primary btn-centered">
					<i class="fas fa-check icon"></i> Validate
				</button>
				<a class="btn-link" href="/verification">@lang('ui.cancel')</a>
			</div>
        {!! BootForm::close() !!}
    </div>
@endsection