@if(count($unverified_attributes))
    <div class="alert alert-danger">
        @if($ready_for_verification)
            <div>
                @lang('verification.unverified_attributes', [
                    'platform_name' => $platform_name
                ])
            </div>
            <a class="btn btn-danger btn-xs pull-right" href="/verification">@lang('verification.btn_verify')</a>            
            <strong>
                <ul>
                    @foreach($unverified_attributes as $attr)
                        <li>
                            @lang('profile.'.$attr)
                        </li>
                    @endforeach
                </ul>
            </strong>
        @else
            @lang('verification.profile_not_completed')
        @endif
    </div>
@elseif($show_email_verification_alert)
    <div class="alert alert-info">
        @lang('profile.email_verification_alert')
        <a class="btn btn-primary btn-xs pull-right" href="/verification/email_code">Verify</a>
    </div>
@endif