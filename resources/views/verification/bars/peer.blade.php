@if($verification->status == 'pending')
    <form method="post" action="/verification/delete/{{$verification->id}}">
        {{ csrf_field() }}
        <a class="btn btn-primary btn-xs" href="/verification/peer_code/{{$verification->id}}">
            @lang('verification.peer.link_code')
        </a>
        <button type="submit" class="btn btn-danger btn-xs">
            @lang('ui.cancel')
        </button>
    </form>
@endif