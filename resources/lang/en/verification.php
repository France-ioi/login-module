<?php
return [

    'header' => 'Verification',
    'header_verifications' => 'Recent verifications',
    'header_methods' => 'Available verification methods',

    'btn_verify' => 'Verify',

    'unverified_attributes' => 'In order to give you access to :platform_name, we need to verify the following information:',
    'profile_not_completed' => 'Please complete your profile. Some fields may be required before you can continue.',
    'user_attributes' => 'Informations',

    'approved_attributes' => 'Verified informations',
    'rejected_attributes' => 'Rejected informations',

    'methods' => [
        'email_code' => 'Sending a code by email',
        'email_domain' => 'Providing a verified email from a french academy domain',
        'id_upload' => 'Uploading a picture of user holding their ID and a code',
        'classroom_upload' => 'Uploading a picture of teacher in his classroom with a few students or colleagues, and holding a code',
        'peer' => 'Having a verified colleague confirm their status',
        'imported_data' => 'Imported data'
    ],


    'states' => [
        'NOT_VERIFIED' => 'Not verified',
        'VERIFICATION_REQUIRED' => 'To verify',
        'IN_PROCESS' => 'Verification in process',
        'ACTION_REQUIRED' => 'Continue to verify',
        'REFRESH_REQUIRED' => 'Verification needs to be refreshed',
        'OBSOLETE' => 'Verification is obsolete',
        'VERIFIED' => 'Verified',
        'REJECTED' => 'Rejected',
    ],

    'btn_profile' => 'Back to profile',

    'email_code' => [
        'help' => 'Input the code received on your email. If you never received it, make sure the email address provided is correct, check your spam folder, then contact us at :email from that email address.',
        'email' => 'Email',
        'code' => 'Code',
        'error' => 'Wrong email verification code.'
    ],

    'email_domain' => [
        'help' => 'Update your email address',
        'role' => 'Which one',
        'account' => 'Account',
        'domain' => 'Accepted domains',
        'user_country_empty' => 'Please go back and select country in your profile',
        'no_country_domains' => 'No academic email domains available for your country',
    ],

    'imported_data' => 'No actions available',

    'id_upload' => [
        'help' => 'Id upload help text here',
        'file' => 'Select file',
        'list' => 'List of fields that this method can verify'
    ],

    'classroom_upload' => [
        'help' => 'Classroom upload help text here'
    ],

    'peer' => [
        'help' => 'Peer teacher validation help text here',
        'email' => 'Email or login',
        'user_not_found' => 'User not found',
        'code' => 'Code',
        'wrong_code' => 'Wrong code',
        'link_code' => 'Enter code',
        'code_help' => 'Help text',
    ],

    'upload' => [
        'file' => 'Select file',
        'file_size' => 'The size of file must not exceed :size megabyte(s). Allowed extensions: .gif .jpg .png',
        'link_view_file' => 'View file'
    ]

];