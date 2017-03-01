@extends('layouts.popup')

@push('login')
    {!! BootForm::text('login', trans('profile.login')) !!}
@endpush

@push('first_name')
    {!! BootForm::text('first_name', trans('profile.first_name'), array_get($values, 'first_name')) !!}
@endpush

@push('last_name')
    {!! BootForm::text('last_name', trans('profile.last_name'), array_get($values, 'last_name')) !!}
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

@push('ministry_of_education_fr')
    <div id="box_ministry_of_education_fr" class="collapse">
        {!! BootForm::checkbox('ministry_of_education_fr', trans('profile.ministry_of_education_fr')) !!}
    </div>
@endpush

@push('ministry_of_education')
    <div id="box_ministry_of_education" class="collapse">
        {!! BootForm::text('ministry_of_education', trans('profile.ministry_of_education')) !!}
    </div>
@endpush

@push('school_grade')
    <div id="box_school_grade" class="collapse">
        {!! BootForm::text('school_grade', trans('profile.school_grade')) !!}
    </div>
@endpush

@push('student_id')
    <div id="box_student_id" class="collapse">
        {!! BootForm::text('student_id', trans('profile.student_id')) !!}
    </div>
@endpush

@push('graduation_year')
    {!! BootForm::text('graduation_year', trans('profile.graduation_year')) !!}
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


@section('content')
@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
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

    <link href="/css/bootstrap-datepicker3.css" rel="stylesheet">
    <script type="text/javascript">
        $(document).ready(function() {
            $('#birthday').datepicker({ format: 'yyyy-mm-dd' });

            function updateHidden() {
                $('#box_ministry_of_education_fr').hide();
                $('#box_ministry_of_education').hide();
                $('#box_school_grade').hide();
                $('#box_student_id').hide();
                var role = $('#role').val();
                if(role == 'student') {
                    $('#box_school_grade').show();
                    $('#box_student_id').show();
                } else if(role == 'teacher') {
                    var country_code = $('#country_code').val();
                    if(country_code == 'fr') {
                        $('#box_ministry_of_education_fr').show();
                    } else {
                        $('#box_ministry_of_education').show();
                    }
                }
            }
            $('#role').change(updateHidden);
            $('#language').change(updateHidden);
            updateHidden();
        })
    </script>
@endsection