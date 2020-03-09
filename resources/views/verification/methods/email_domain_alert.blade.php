@extends('layouts.popup')

@section('content')
    <div class="panel-body">
		<div class="sectionTitle">
			<i class="fas fa-landmark icon"></i>
            @lang('verification.methods.email_domain')
        </div>
        <p class="help-block">@lang('verification.email_domain.'.$alert)</p>
        <a class="btn btn-rounded btn-centered btn-default" href="/profile">@lang('verification.btn_profile')</a>
    </div>

@endsection
