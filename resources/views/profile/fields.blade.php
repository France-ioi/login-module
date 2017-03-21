@push('login')
    {!! BootForm::text('login', trans('profile.login')) !!}
@endpush

@push('first_name')
    {!! BootForm::text('first_name', trans('profile.first_name')) !!}
@endpush

@push('last_name')
    {!! BootForm::text('last_name', trans('profile.last_name')) !!}
@endpush

@push('last_name')
    {!! BootForm::hidden('real_name_visible', 0) !!}
    {!! BootForm::checkbox('real_name_visible', trans('profile.real_name_visible')) !!}
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

@push('timezone')
    {!! BootForm::select('timezone', trans('profile.timezone'), trans('timezones')) !!}
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
    {!! BootForm::text('school_grade', trans('profile.school_grade')) !!}
@endpush

@push('student_id')
    {!! BootForm::text('student_id', trans('profile.student_id')) !!}
@endpush

@push('graduation_year')
    {!! BootForm::date('graduation_year', trans('profile.graduation_year')) !!}
@endpush

@push('gender')
    {!! BootForm::radios('gender', trans('profile.gender'), trans('profile.genders')) !!}
@endpush

@push('birthday')
    {!! BootForm::date('birthday', trans('profile.birthday')) !!}
@endpush

@push('presentation')
    {!! BootForm::textarea('presentation', trans('profile.presentation')) !!}
@endpush

@push('website')
    {!! BootForm::text('website', trans('profile.website')) !!}
@endpush