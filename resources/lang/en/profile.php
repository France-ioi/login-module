<?php

return [
    'header' => 'Profile',
    'optional_fields_filter' => 'Display required or recommended fields only',

    'login' => 'Login',
    'first_name' => 'First name',
    'last_name' => 'Last name',
    'real_name_visible' => 'Display my real name on my public profile',
    'primary_email'  => 'Primary email',
    'secondary_email'  => 'Secondary email',
    'language' => 'Language',
    'nationality' => 'Nationality',
    'country_code' => 'Country of residence',
    'address' => 'Address',
    'city' => 'City',
    'zipcode' => 'Zip code',
    'timezone' => 'Timezone',
    'primary_phone' => 'Primary phone',
    'secondary_phone' => 'Secondary phone',
    'role' => 'Role',
    'roles' => [
        'student' => 'Student',
        'teacher' => 'Teacher',
        'other' => 'Other'
    ],
    'ministry_of_education' => 'What country\'s ministry of education is your school attached to',
    'ministry_of_education_fr' => 'in a public school or school under contract with the french ministry of education',
    'school_grade' => 'School grade',
    'student_id' => 'Student ID',
    'graduation_year' => 'Graduation year',
    'birthday'  => 'Birthday',
    'gender' => 'Gender',
    'genders' => [
        'm' => 'Male',
        'f' => 'Female'
    ],
    'presentation'  => 'Presentation',
    'website' => 'Website',
    'picture' => 'Picture',
    'picture_size_error' => 'The picture may not be greater than :size megabyte(s).',
    'graduation_grade_range' => 'Grade for the school year :year_begin-:year_end',
    'graduation_grade' => 'Grade',
    'success' => 'Profile updated.',

    'primary_email_verification_code' => 'Primary email verification code',
    'secondary_email_verification_code' => 'Secondary email verification code',
    'email_verification_help' => 'Input the code received on your email. If you never received it, make sure the email address provided is correct, check your spam folder, then contact us at :email from that email address.',
    'email_verified' => 'Verified',
    'email_verification_code_error' => 'Wrong email verification code.',

    'pms_redirect_msg' => 'As a PMS user, you must edit your profile directly on PMS. Please press the "Zurück zum JwInf" button afterwards to have your profile updated here.',
    'pms_redirect_btn' => 'Proceed to PMS',

    'teacher_domain_verified' => 'Do you have an email from an authorized domain?',
    'teacher_domain_options' => [
        'yes' => 'Yes, I will provide it as my primary/secondary email',
        'no' => 'No, I don\'t have one'
    ],
    'teacher_domain_alert' => 'Please contact :email and explain why you don\'t have an authorized email.',
    'login_change_limitations' => 'If you change your login, after one hour, you won\'t be able to change it again for a year',
    'login_change_required' => 'Sorry, we need to you to pick a new login. This one was already used or doesn\'t follow the rules. Only lowercase letters, digits and -  are allowed',

    'verification_alert_p1' => 'The following information was verified. If you change it, you may need to have it verified again or you may lose access to some platforms or features: ',
    'verification_alert_p2' => 'Are you sure you want to change them?',

    'email_verification_alert' => 'Email not verified',

    'profile_filter' => 'In order to give you access to :platform_name, you need to change the following information:',

    'required_fields_explanation' => 'Only fields marked with (*) are required',
    'collected_data' => 'Manage or delete data collected about me',

    'tooltips' => [
        'login' => 'Avoid including your real name in your login, as it will be made publicly visible. Instead, use a pseudonym that you can easily remember.',
        'first_name' => 'Knowing your name makes it possible for us to give you access to your account if you lose access, and didn’t provide an email address that you have access to. You may also decide to make it public if you want other users to know who you are (see field below). This field is required if you want to participate officially to contests.',
        'last_name' => 'Knowing your name makes it possible for us to give you access to your account if you lose access, and didn’t provide an email address that you have access to. You may also decide to make it public if you want other users to know who you are (see field below). This field is required if you want to participate officially to contests.',
        'grade' => 'Knowing your grade makes it possible for us to determine what events you can participate in (training camps we organize, for example), or to rank you in the correct category when you participate in online contests.',
        'graduation_year' => 'Knowing your graduation year makes it possible for us to determine what events you can participate in (training camps we organize, for example), or to rank you in the correct category when you participate in online contests.',
        'country_code' => 'Knowing your country of residence makes it possible for us to determine what events you can participate in (training camps we organize, for example), or to rank you in the correct category when you participate in online contests.',
        'role' => 'Knowing and checking your role, makes it possible to give you access to tools reserved for some types of users.',
        'primary_email' => 'Knowing your email address gives us a way to help you if you lost password, or contact you about events or content you might be interested in (if you accept, see below).',
        'secondary_email' => 'Knowing your email address gives us a way to help you if you lost password, or contact you about events or content you might be interested in (if you accept, see below).',
        'language' => 'This information will be used to determine in what language the platform and content should be displayed.',
        'birthday' => 'Knowing your birthday makes it possible for us to determine what events you can participate in (training camps we organize, for example), or to rank you in the correct category when you participate in online contests.',
        'gender' => 'Knowing your genre will help us publish some general statistics about who uses our platform, as well as analyze if some content should be better adapted to different genders.',
        'presentation' => 'This is information about you you decide to make public to all users.',
        'website' => 'This is information about you you decide to make public to all users.',
        'student_id' => 'Only provide this information if required by your school. This will make it possible for your teachers to check who you are.'
    ],


    'sections' => [
        'personal' => 'Personal informations',
        'school' => 'Scholar informations',
        'contact' => 'Contact informations',
        'settings' => 'Parameters',
    ],
    'quick_menu' => 'Quick menu'
];
