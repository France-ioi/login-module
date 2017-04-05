@push('login')
    {!! BootForm::text('login', trans('profile.login').(isset($required['login']) ? $star : '')) !!}
@endpush

@push('first_name')
    {!! BootForm::text('first_name', trans('profile.first_name').(isset($required['first_name']) ? $star : '')) !!}
@endpush

@push('last_name')
    {!! BootForm::text('last_name', trans('profile.last_name').(isset($required['last_name']) ? $star : '')) !!}
@endpush

@push('last_name')
    {!! BootForm::hidden('real_name_visible', 0) !!}
    {!! BootForm::checkbox('real_name_visible', trans('profile.real_name_visible')) !!}
@endpush

@push('primary_email')
    {!! BootForm::email('primary_email', trans('profile.primary_email').(isset($required['primary_email']) ? $star : '')) !!}
@endpush

@push('secondary_email')
    {!! BootForm::email('secondary_email', trans('profile.secondary_email').(isset($required['secondary_email']) ? $star : '')) !!}
@endpush

@push('language')
    {!! BootForm::select('language', trans('profile.language').(isset($required['language']) ? $star : ''), config('app.locales')) !!}
@endpush

@push('country_code')
    {!! BootForm::select('country_code', trans('profile.country_code').(isset($required['country_code']) ? $star : ''), ['' => '...'] + trans('countries')) !!}
@endpush

@push('address')
    {!! BootForm::text('address', trans('profile.address').(isset($required['address']) ? $star : '')) !!}
@endpush

@push('city')
    {!! BootForm::text('city', trans('profile.city').(isset($required['city']) ? $star : '')) !!}
@endpush

@push('zipcode')
    {!! BootForm::text('zipcode', trans('profile.zipcode').(isset($required['zipcode']) ? $star : '')) !!}
@endpush

@push('timezone')
    {!! BootForm::select('timezone', trans('profile.timezone').(isset($required['timezone']) ? $star : ''), ['' => '...'] + trans('timezones')) !!}
@endpush

@push('primary_phone')
    {!! BootForm::text('primary_phone', trans('profile.primary_phone').(isset($required['primary_phone']) ? $star : '')) !!}
@endpush

@push('secondary_phone')
    {!! BootForm::text('secondary_phone', trans('profile.secondary_phone').(isset($required['secondary_phone']) ? $star : '')) !!}
@endpush

@push('role')
    {!! BootForm::select('role', trans('profile.role').(isset($required['role']) ? $star : ''), trans('profile.roles')) !!}
@endpush

@push('ministry_of_education_fr')
    <div id="box_ministry_of_education_fr" class="collapse">
        {!! BootForm::hidden('ministry_of_education_fr', 0) !!}
        {!! BootForm::checkbox('ministry_of_education_fr', trans('profile.ministry_of_education_fr')) !!}
    </div>
@endpush

@push('ministry_of_education')
    <div id="box_ministry_of_education" class="collapse">
        {!! BootForm::text('ministry_of_education', trans('profile.ministry_of_education')) !!}
    </div>
@endpush

@push('school_grade')
    {!! BootForm::text('school_grade', trans('profile.school_grade').(isset($required['school_grade']) ? $star : '')) !!}
@endpush

@push('student_id')
    {!! BootForm::text('student_id', trans('profile.student_id').(isset($required['student_id']) ? $star : '')) !!}
@endpush

@push('graduation_year')
    {!! BootForm::text('graduation_year', trans('profile.graduation_year').(isset($required['graduation_year']) ? $star : '')) !!}
@endpush

@push('gender')
    {!! BootForm::radios('gender', trans('profile.gender').(isset($required['gender']) ? $star : ''), trans('profile.genders')) !!}
@endpush

@push('birthday')
    {!! BootForm::date('birthday', trans('profile.birthday').(isset($required['birthday']) ? $star : '')) !!}
@endpush

@push('presentation')
    {!! BootForm::textarea('presentation', trans('profile.presentation').(isset($required['presentation']) ? $star : '')) !!}
@endpush

@push('website')
    {!! BootForm::text('website', trans('profile.website').(isset($required['website']) ? $star : '')) !!}
@endpush