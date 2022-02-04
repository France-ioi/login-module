@if(count($uncompleted_attributes))
    <div class="alert alert-danger">
        <div>
            @lang('profile.not_completed')
        </div>
        <strong>
            <ul>
                @foreach($uncompleted_attributes as $attr)
                    <li>
                        @lang('profile.'.$attr)
                    </li>
                @endforeach
            </ul>
        </strong>
    </div>
@endif