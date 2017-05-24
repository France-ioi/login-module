@push('login')
    {!! BootForm::text(
        'login',
        trans('profile.login').(isset($starred['login']) ? $star : ''),
        null,
        isset($fixed['login']) ? ['disabled'] : []
    ) !!}
@endpush

@push('first_name')
    {!! BootForm::text(
        'first_name',
        trans('profile.first_name').(isset($starred['first_name']) ? $star : ''),
        null,
        isset($fixed['first_name']) ? ['disabled'] : []
    ) !!}
@endpush

@push('last_name')
    {!! BootForm::text(
        'last_name',
        trans('profile.last_name').(isset($starred['last_name']) ? $star : ''),
        null,
        isset($fixed['last_name']) ? ['disabled'] : []
    ) !!}
@endpush

@push('last_name')
    {!! BootForm::hidden('real_name_visible', 0) !!}
    {!! BootForm::checkbox(
        'real_name_visible',
        trans('profile.real_name_visible')
    ) !!}
@endpush

@push('primary_email')
    {!! BootForm::email(
        'primary_email',
        trans('profile.primary_email').(isset($starred['primary_email']) ? $star : ''),
        null,
        isset($fixed['primary_email']) ? ['disabled'] : []
    ) !!}
    @if($user->primary_email_id)
        @if($user->primary_email_verified)
            <div class="alert alert-success">
                @lang('profile.email_verified')
            </div>
        @else
            {!! BootForm::text(
                'primary_email_verification_code',
                trans('profile.primary_email').trans('profile.email_verification_code').(isset($starred['primary_email_verified']) ? $star : '')
            ) !!}
            <div class="form-group">
                <p class="help-block">
                    @lang('profile.email_verification_help', [
                        'role' => trans('profile.primary_email_role'),
                        'email' => '<a href="mailto:'.config('mail.from.address').'">'.config('mail.from.address').'</a>'
                    ])
                </p>
            </div>
        @endif
    @endif
@endpush

@push('secondary_email')
    {!! BootForm::email(
        'secondary_email',
        trans('profile.secondary_email').(isset($starred['secondary_email']) ? $star : ''),
        null,
        isset($fixed['secondary_email']) ? ['disabled'] : []
    ) !!}
    @if($user->secondary_email_id)
        @if($user->secondary_email_verified)
            <div class="alert alert-success">
                @lang('profile.email_verified')
            </div>
        @else
            {!! BootForm::text(
                'secondary_email_verification_code',
                trans('profile.secondary_email').trans('profile.email_verification_code')).(isset($starred['secondary_email_verified']) ? $star : '')
            !!}
            <div class="form-group">
                <p class="help-block">
                    @lang('profile.email_verification_help', [
                        'role' => trans('profile.secondary_email_role'),
                        'email' => '<a href="mailto:'.config('mail.from.address').'">'.config('mail.from.address').'</a>'
                    ])
                </p>
            </div>
        @endif
    @endif
@endpush

@push('language')
    {!! BootForm::select(
        'language',
        trans('profile.language').(isset($starred['language']) ? $star : ''),
        config('app.locales'),
        null,
        isset($fixed['language']) ? ['disabled'] : []
    ) !!}
@endpush

@push('country_code')
    {!! BootForm::select(
        'country_code',
        trans('profile.country_code').(isset($starred['country_code']) ? $star : ''),
        ['' => '...'] + trans('countries'),
        null,
        isset($fixed['country_code']) ? ['disabled'] : []
    ) !!}
@endpush

@push('address')
    {!! BootForm::text(
        'address',
        trans('profile.address').(isset($starred['address']) ? $star : ''),
        null,
        isset($fixed['address']) ? ['disabled'] : []
    ) !!}
@endpush

@push('city')
    {!! BootForm::text(
        'city',
        trans('profile.city').(isset($starred['city']) ? $star : ''),
        null,
        isset($fixed['city']) ? ['disabled'] : []
    ) !!}
@endpush

@push('zipcode')
    {!! BootForm::text(
        'zipcode',
        trans('profile.zipcode').(isset($starred['zipcode']) ? $star : ''),
        null,
        isset($fixed['zipcode']) ? ['disabled'] : []
    ) !!}
@endpush

@push('timezone')
    {!! BootForm::select(
        'timezone',
        trans('profile.timezone').(isset($starred['timezone']) ? $star : ''),
        ['' => '...'] + trans('timezones'),
        null,
        isset($fixed['timezone']) ? ['disabled'] : []
    ) !!}
@endpush

@push('primary_phone')
    {!! BootForm::text(
        'primary_phone',
        trans('profile.primary_phone').(isset($starred['primary_phone']) ? $star : ''),
        null,
        isset($fixed['primary_phone']) ? ['disabled'] : []
    ) !!}
@endpush

@push('secondary_phone')
    {!! BootForm::text(
        'secondary_phone',
        trans('profile.secondary_phone').(isset($starred['secondary_phone']) ? $star : ''),
        null,
        isset($fixed['secondary_phone']) ? ['disabled'] : []
    ) !!}
@endpush

@push('role')
    {!! BootForm::select(
        'role',
        trans('profile.role').(isset($starred['role']) ? $star : ''),
        trans('profile.roles'),
        null,
        isset($fixed['role']) ? ['disabled'] : []
    ) !!}
@endpush

@push('ministry_of_education_fr')
    <div id="box_ministry_of_education_fr" class="collapse">
        {!! BootForm::hidden('ministry_of_education_fr', 0) !!}
        {!! BootForm::checkbox(
            'ministry_of_education_fr',
            trans('profile.ministry_of_education_fr')
        ) !!}
    </div>
@endpush

@push('ministry_of_education')
    <div id="box_ministry_of_education" class="collapse">
        {!! BootForm::text(
            'ministry_of_education',
            trans('profile.ministry_of_education'),
            null,
            isset($fixed['ministry_of_education']) ? ['disabled'] : []
        ) !!}
    </div>
@endpush

@push('school_grade')
    {!! BootForm::text(
        'school_grade',
        trans('profile.school_grade').(isset($starred['school_grade']) ? $star : ''),
        null,
        isset($fixed['school_grade']) ? ['disabled'] : []
    ) !!}
@endpush

@push('student_id')
    {!! BootForm::text(
        'student_id',
        trans('profile.student_id').(isset($starred['student_id']) ? $star : ''),
        null,
        isset($fixed['student_id']) ? ['disabled'] : []
    ) !!}
@endpush

@push('graduation_year')
    {!! BootForm::text(
        'graduation_year',
        trans('profile.graduation_year').(isset($starred['graduation_year']) ? $star : ''),
        null,
        isset($fixed['graduation_year']) ? ['disabled'] : []
    ) !!}
@endpush

@push('gender')
    {!! BootForm::radios(
        'gender',
        trans('profile.gender').(isset($starred['gender']) ? $star : ''),
        trans('profile.genders'))
    !!}
@endpush

@push('birthday')
    {!! BootForm::date(
        'birthday',
        trans('profile.birthday').(isset($starred['birthday']) ? $star : ''),
        null,
        isset($fixed['birthday']) ? ['disabled'] : []
    ) !!}
@endpush

@push('presentation')
    {!! BootForm::textarea(
        'presentation',
        trans('profile.presentation').(isset($starred['presentation']) ? $star : ''),
        null,
        isset($fixed['presentation']) ? ['disabled'] : []
    ) !!}
@endpush

@push('website')
    {!! BootForm::text(
        'website',
        trans('profile.website').(isset($starred['website']) ? $star : ''),
        null,
        isset($fixed['website']) ? ['disabled'] : []
    ) !!}
@endpush