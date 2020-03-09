@extends('layouts.popup')

@section('content')
    <div class="panel-body">
        <div class="sectionTitle">
            <i class="fas fa-shield-alt icon"></i>
            @lang('verification.header_methods')
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
@endsection
