@extends('layouts.popup')

@section('aside')
    @include('layouts.components.left_menu')
@endsection

@section('content')
    <div class="alert-section">
        @include('profile.alerts.filter')
        @include('profile.alerts.profile_not_completed')
        @include('profile.alerts.verification')
        @include('profile.alerts.revalidation')
        @include('profile.alerts.login_change_required')
        @include('profile.alerts.pms_redirect')
        @include('ui.status')
        @include('ui.errors')
    </div>
    <div class="info-message">
        @lang('profile.required_fields_explanation')
        @if($schema->hasRequiredAttributes())
            <div class="checkboxSwitch">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="optional_fields_filter"/>
                        @lang('profile.optional_fields_filter')
                        <span class="bg"><span class="cursor"></span></span>
                    </label>
                </div>
            </div>
        @endif
    </div>
    <div class="panel-body">
        {!! BootForm::horizontal(array_merge($form, ['class' => 'profileForm form-horizontal'])) !!}
            @if($optional_fields_visible)
                {!! BootForm::hidden('optional_fields_visible', 1) !!}
            @endif
            {!! ProfileFormRenderer::render($schema, $user) !!}
            <div class="form-group">
				<button type="submit" class="btn btn-primary btn-centered btn-rounded">
					<i class="fas fa-check icon"></i>
                    @lang('ui.save')
                </button>
                @if($cancel_url)
                    <a class="btn btn-link" href="{{ $cancel_url }}">
                        @lang('ui.close')
                    </a>
                @endif
            </div>
        {!! BootForm::close() !!}
    </div>

    @if($suggested_login)
        <div id="suggested_login_text" style="display: none">
            @lang('auth.suggested_login', [
                'login' => '<strong>'.$suggested_login.'</strong>'
            ])
        </div>
    @endif

    <script type="text/javascript">
        $(document).ready(function() {
            var options = {
                login_validator: {!! json_encode(config('profile.login_validator')) !!},
                official_domains: {!! json_encode($official_domains) !!},
                app_locale: {!! json_encode(app()->getLocale()) !!},
                optional_fields_visible: {!! json_encode($optional_fields_visible) !!},
                verified_attributes: {!! json_encode($verified_attributes) !!},
                tooltips: {!! json_encode(trans('profile.tooltips')) !!}
            }            
            window.components.profile_editor(options);
        });
    </script>



    <div class="modal fade" tabindex="-1" role="dialog" id="verification_alert">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('ui.alert')</h4>
                </div>
                <div class="modal-body">
                    <p>@lang('profile.verification_alert_p1')</p>
                    <ul>
                        @foreach($verified_attributes as $attr)
                            <li id="verification_alert_{{$attr}}">@lang('profile.'.$attr)</li>
                        @endforeach
                    </ul>
                    <p></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('ui.cancel')</button>
                    <button type="button" class="btn btn-primary" id="verification_alert_save">@lang('ui.save')</button>
                </div>
            </div>
        </div>
    </div>

@endsection
