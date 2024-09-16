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
            <button class="btn btn-danger btn-xs btn-goto pull-right" data-target="/verification">@lang('verification.btn_verify')</button>
        @else
            @lang('verification.profile_not_completed')
        @endif
    </div>
@elseif($show_email_verification_alert)
    <div class="alert alert-info">
        @lang('profile.email_verification_alert')
        <button class="btn btn-primary btn-xs btn-goto pull-right" data-target="/verification/email_code">@lang('verification.btn_verify')</button>
    </div>
@endif