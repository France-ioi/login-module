@extends('layouts.popup')

@section('content')
	<div class="panel panel-default">
		<div class="alert-section">
			<div class="alert alert-danger">
				<i class="fas fa-bell icon"></i>
				@lang('auth_methods.alert')
			</div>
		</div>
		<div class="panel-body">
			@include('auth_methods.auth_connections')
			@include('auth_methods.badges')

		</div>
	</div>

    @if($cancel_url)
        <a class="btn btn-link" href="{{ $cancel_url }}">
            @lang('ui.close')
        </a>
    @endif
@endsection