@extends('layouts.popup')

@section('content')
	@if(count($errors) > 0)
		<div class="alert-section">
			@include('ui.errors')
		</div>
	@endif

    <div class="panel-body">
		<div class="sectionTitle">
			<i class="fas fa-user icon"></i>
            @lang('verification.methods.peer')
        </div>
            <p class="help-block">@lang('verification.peer.help')</p>
            {!! BootForm::horizontal(['url' => '/verification/peer', 'class' => 'form-horizontal verificationForm']) !!}
		{!! BootForm::text('email', trans('verification.peer.email'), null, [
			'prefix' => BootForm::addonText('Aa'),
			'placeholder' => 'email@email.com / Eniac123'
		]) !!}
				<div class="form-group text-center">
					<button type="submit" class="btn btn-rounded btn-primary btn-centered">
						<i class="fas fa-check icon"></i>
						@lang('ui.save')
					</button>

					<a class="btn-link" href="/verification">@lang('ui.close')</a>
				</div>
            {!! BootForm::close() !!}
    </div>

@endsection
