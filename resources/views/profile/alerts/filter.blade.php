@if(count($rejected_attributes))
    <div class="alert alert-danger">
        <div>
            @lang('profile.profile_filter', [
                'platform_name' => $platform_name
            ])
        </div>
        <strong>
            @foreach($rejected_attributes as $attr)
                @lang('profile.'.$attr)@if(!$loop->last), @endif
            @endforeach
        </strong>
    </div>
@endif