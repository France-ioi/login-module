@extends('layouts.popup')

@section('content')
    @if(count($unverified_attributes) == 0)
        <div class="alert-section">
            <div class="alert alert-success">@lang('verification.completed')</div>
        </div>
    @elseif($any_methods_available)
        <div class="alert-section">
            <div class="alert alert-danger">
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
            </div>
	    </div>
    @endif


    @if($any_methods_available)
        @if(count($unverified_attributes) > 0)    
            <div class="panel-body">
                <strong>@lang('verification.methods_header')</strong>
            </div>
        @endif

        @include('verification.methods_list', [
            'title' => trans('verification.header_recommended_methods'),
            'methods' => $recommended_methods
        ])
        @include('verification.methods_list', [
            'title' => trans('verification.header_alternative_methods'),
            'methods' => $alternative_methods
        ])
        @include('verification.methods_list', [
            'title' => trans('verification.header_optional_methods'),
            'methods' => $optional_methods
        ])
    @else
        <div class="alert-section">
            <div class="alert alert-success">@lang('verification.not_required')</div>
        </div>
    @endif


    @if(count($verifications))
        <div class="panel-body">
            <div class="sectionTitle">
                <i class="fas fa-calendar-check icon"></i>
                @lang('verification.header_verifications')
            </div>
            <ul class="list-group">
                @foreach($verifications as $verification)
                    @if($verification->method->name == 'email_domain' && $verification->status == 'pending')
                        <a class="list-group-item" href="/verification/email_domain/input_code/{{ $verification->id }}">
                    @else
                        <li class="list-group-item">
                    @endif
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
                            @lang('verification.approved_attributes'):
                            @foreach($verification->user_attributes as $attr)
                                @lang('profile.'.$attr)@if(!$loop->last), @endif
                            @endforeach
                        </div>
                        @if($verification->status != 'pending')
                            @if(count($verification->rejected_attributes))
                                <div class="alert alert-warning">
                                    @lang('verification.rejected_attributes'):
                                    @foreach($verification->rejected_attributes as $attr)
                                        @lang('profile.'.$attr)@if(!$loop->last), @endif
                                    @endforeach
                                </div>
                            @endif
                            @if(!is_null($verification->message))
                                <pre>{!! $verification->message !!}</pre>
                            @endif
                        @endif
                        <div class="txtright">
                            @include('verification.bars.'.$verification->method->name)
                        </div>
                    @if($verification->method->name == 'email_domain' && $verification->status == 'pending')
                    </a>
                    @else
                    </li>
                    @endif
                @endforeach
            </ul>
	</div>
    @endif


    <div class="panel-body">
        <a class="btn btn-rounded btn-centered btn-primary" href="/profile">@lang('verification.btn_profile')</a>

        @if(!count($unverified_attributes))
            <a class="btn btn-default btn-centered btn-rounded" href="{!! $continue_url !!}">@lang('ui.continue')</a>
        @endif
    </div>
@endsection
