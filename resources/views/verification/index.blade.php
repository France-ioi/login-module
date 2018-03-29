@extends('layouts.popup')

@section('content')
    @if(count($unverified_attributes))
        <div class="alert alert-danger">
            <strong>@lang('verification.unverified_attributes')</strong>:
            @foreach($unverified_attributes as $attr)
                @lang('profile.'.$attr)@if(!$loop->last), @endif
            @endforeach
        </div>
    @endif


    @if(count($verifications))
        <div class="panel panel-default">
            <div class="panel-heading">
                @lang('verification.header_verifications')
            </div>
            <ul class="list-group">
                @foreach($verifications as $verification)
                    <li class="list-group-item">
                        <div class="pull-right">
                            {!! Verification::stateLabel($verification) !!}
                        </div>
                        <div>
                            <i>{{ $verification->created_at }}</i>
                        </div>
                        <strong>
                            @lang('verification.methods.'.$verification->method->name)
                        </strong>
                        <div>
                            @lang('verification.user_attributes'):
                            @foreach($verification->user_attributes as $attr)
                                @lang('profile.'.$attr)@if(!$loop->last), @endif
                            @endforeach
                        </div>
                        <div>
                            @include('verification.bars.'.$verification->method->name)
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="panel panel-default">
        <div class="panel-heading">
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

    <div class="panel panel-default">
        <div class="panel-body">
            <a class="btn btn-default" href="/profile">@lang('verification.btn_profile')</a>
        </div>
    </div>
@endsection