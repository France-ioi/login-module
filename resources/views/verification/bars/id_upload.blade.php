@if($verification->status == 'pending')
    <form method="post" action="/verification/delete/{{$verification->id}}">
        {{ csrf_field() }}
        <span class="actionsButtons">
        	<a class="btn-link" href="{{ Storage::url('verifications/' . $verification->file) }}" target="_blank">
	        	<i class="fa fa-eye icon"></i>
            	@lang('verification.upload.link_view_file')
	        </a>
	        <button type="submit" class="btn-link">
		        <i class="far fa-trash-alt icon"></i>
    	        @lang('ui.delete')
	        </button>
		</span>
    </form>
@endif
