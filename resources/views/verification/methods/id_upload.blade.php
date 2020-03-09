@extends('layouts.popup')

@section('content')

	@if(count($errors) > 0)
		<div class="alert-section">
			@include('ui.errors')
		</div>
	@endif

    <div class="panel-body">
		<div class="sectionTitle">
			<i class="fas fa-id-card icon"></i>
            @lang('verification.methods.id_upload')
        </div>
        <p class="help-block">@lang('verification.id_upload.help')</p>
		{!! BootForm::horizontal(['url' => '/verification/id_upload', 'files' => true, 'class' => 'form-horizontal verificationForm' ]) !!}
			<fieldset>
			    <div class="form-group">
					<label class="control-label col-sm-2 col-md-3">The code</label>
					<div class="col-sm-10 col-md-9">
						<div class="text-center text-lg text-colored">{{$code}}</div>
					</div>
			    </div>
			</fieldset>
			<fieldset>
               {!! BootForm::file(
                    'file',
                    trans('verification.upload.file'),
                    [
                        'accept' => '.gif,.jpg,.png',
						'max_file_size' => $max_file_size,
						'help_text' => trans('verification.upload.file_size', ['size' => $max_file_size])
   
                    ]
				)!!}
			</fieldset>
			<fieldset>
				<legend class="sectionTitle sm">@lang('verification.id_upload.list')</legend>
				<ul class="list-group">
					@foreach($method->user_attributes as $attribute)
					<li class="list-group-item">
			            <div class="checkboxSwitch">
            			    <div class="checkbox">
			                    <label>
            			            <input type="checkbox" name="user_attributes[]" value={{$attribute}} />
			                        @lang('profile.'.$attribute)
			                        <span class="bg"><span class="cursor"></span></span>
        			            </label>
			                </div>
			            </div>
					</li>
					@endforeach
				</ul>
			</fieldset>
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
