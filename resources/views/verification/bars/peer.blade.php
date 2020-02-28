@if($verification->status == 'pending')
    <form method="post" action="/verification/delete/{{$verification->id}}">
        {{ csrf_field() }}
        <span class="actionsButtons">
	        <a class="btn btn-primary btn-xs" href="/verification/peer_code/{{$verification->id}}">
	        	<i class="fa fa-eye icon"></i>
    	        @lang('verification.peer.link_code')
	        </a>
	        <button type="submit" class="btn btn-danger btn-xs">
	        	<i class="far fa-trash-alt icon"></i>
	            @lang('ui.delete')
	        </button>
		</span>
    </form>
@endif
