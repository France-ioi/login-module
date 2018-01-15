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
    'country_code' => 'Сountry',
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
    'graduation_grade' => 'Grade for the school year :year_begin-:year_end',
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
    'login_change_required' => 'Sorry, we need to you to pick a new login. This one was already used or doesn\'t follow the rules. Only lowercase letters, digits and -  are allowed'
];
