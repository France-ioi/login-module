@if($verification->status == 'pending')
    <form method="post" action="/verification/delete/{{$verification->id}}">
        {{ csrf_field() }}
        <a class="btn btn-primary btn-xs" href="/verifications/{{$verification->file}}" target="_blank">
            @lang('verification.upload.link_view_file')
        </a>
        <button type="submit" class="btn btn-danger btn-xs">
            @lang('ui.cancel')
        </button>
    </form>
@elseif($verification->status == 'rejected' && !is_null($verification->data))
    <pre>{!! $verification->data !!}</pre>
@endif