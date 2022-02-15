@extends('layouts.client_admin')

@section('content')
    <h3>User {{ $user->id }}</h3>

    {!! BootForm::horizontal(array_merge($form, ['class' => 'profileForm form-horizontal'])) !!}
        <input type="hidden" name="refer_page" value="{{ $refer_page }}"/>
        {!! ProfileFormRenderer::render($schema, $user) !!}
        <button type="submit" class="btn btn-primary">Save</button>
        <a class="btn btn-primary" href="{{ $refer_page }}">Cancel</a>
    {!! BootForm::close() !!}


    <script>
        $(document).ready(function() {
            var options = {
                login_validator: {!! json_encode(config('profile.login_validator')) !!},
                official_domains: {!! json_encode($official_domains) !!},
                app_locale: {!! json_encode(app()->getLocale()) !!},
                optional_fields_visible: true,
                verified_attributes: [],
                tooltips: {!! json_encode(trans('profile.tooltips')) !!}
            }            
            window.components.profile_editor(options);
        })
    </script>
@endsection