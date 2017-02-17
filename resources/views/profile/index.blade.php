@extends('layouts.popup')

@push('login')
    {!! BootForm::text('login', trans('profile.login')) !!}
@endpush

@push('first_name')
    {!! BootForm::text('first_name', trans('profile.first_name')) !!}
@endpush

@push('last_name')
    {!! BootForm::text('last_name', trans('profile.last_name')) !!}
@endpush

@push('primary_email')
    {!! BootForm::email('primary_email', trans('profile.primary_email')) !!}
@endpush

@push('secondary_email')
    {!! BootForm::email('secondary_email', trans('profile.secondary_email')) !!}
@endpush

@push('language')
    {!! BootForm::select('language', trans('profile.language'), config('app.locales')) !!}
@endpush

@push('country_code')
    {!! BootForm::select('country_code', trans('profile.country_code'), trans('countries')) !!}
@endpush

@push('address')
    {!! BootForm::text('address', trans('profile.address')) !!}
@endpush

@push('city')
    {!! BootForm::text('city', trans('profile.city')) !!}
@endpush

@push('zipcode')
    {!! BootForm::text('zipcode', trans('profile.zipcode')) !!}
@endpush

@push('primary_phone')
    {!! BootForm::text('primary_phone', trans('profile.primary_phone')) !!}
@endpush

@push('secondary_phone')
    {!! BootForm::text('secondary_phone', trans('profile.secondary_phone')) !!}
@endpush

@push('role')
    {!! BootForm::select('role', trans('profile.role'), trans('profile.roles')) !!}
@endpush

@push('birthday')
    {!! BootForm::date('birthday', trans('profile.birthday')) !!}
@endpush

@push('presentation')
    {!! BootForm::textarea('presentation', trans('profile.presentation')) !!}
@endpush


@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">Profile</div>
        <div class="panel-body">
            {!! BootForm::open(['url' => '/profile', 'method' => 'post']) !!}
                @foreach($fields as $field)
                    @stack($field)
                @endforeach
                {!! BootForm::submit(trans('auth.btn_save')) !!}
            {!! BootForm::close() !!}
        </div>
    </div>
@endsection