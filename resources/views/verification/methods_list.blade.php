@if(count($methods))
    <div class="panel-body">
        <div class="sectionTitle">
            <i class="fas fa-check icon"></i>
            {{ $title }}
        </div>
        <ul class="list-group">
            @foreach($methods as $method)
                <a href="/verification/{{$method->name}}" class="list-group-item">
                    <strong>@lang('verification.methods.'.$method->name)</strong>
                    <div>
                        @lang('verification.user_attributes'):
                        @foreach($method->user_attributes as $attr)
                            @lang('profile.'.$attr)@if(!$loop->last), @endif
                        @endforeach
                    </div>
                </a>
            @endforeach
        </ul>
    </div>
@endif        