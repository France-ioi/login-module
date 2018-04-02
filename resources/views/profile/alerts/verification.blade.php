@if(count($unverified_attributes))
    <div class="alert alert-danger">
        @if($verification_ready)
            <div>
                @lang('verification.unverified_attributes', [
                    'platform_name' => $platform_name
                ])
            </div>
            <strong>
                @foreach($unverified_attributes as $attr)
                    @lang('profile.'.$attr)@if(!$loop->last), @endif
                @endforeach
            </strong>
            <a class="btn btn-default btn-xs pull-right" href="/verification">@lang('verification.btn_verify')</a>
        @else
            @lang('verification.profile_not_completed')
        @endif
    </div>
@endif