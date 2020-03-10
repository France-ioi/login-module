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
	        <p class="help-block">@lang('verification.peer.code_help')</p>
	        {!! BootForm::horizontal(['url' => '/verification/peer_code/'.$verification->id, 'class' => 'form-horizontal verificationForm']) !!}
			{!! BootForm::text('code', trans('verification.peer.code'), null, [
				'placeholder' => trans('badges.code'),
				'prefix' => BootForm::addonIcon('key fas')
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
