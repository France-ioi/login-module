<?php
return [

    'header' => 'Verification',
    'not_required' => 'No verifications are required to use this platform.',
    'header_verifications' => 'Recent verifications',
    'header_recommended_methods' => 'Recommended verification methods for required verifications',
    'header_alternative_methods' => 'Alternative methods for required verifications',
    'header_optional_methods' => 'Other methods, for optional verifications',

    'btn_verify' => 'Verify',

    'completed' => 'All the required verifications of your profile have been validated.',

    'unverified_attributes' => 'In order to give you access to :platform_name, we need to verify the following information:',
    'profile_not_completed' => 'Some fields require verfification before you can continue.',
    'user_attributes' => 'Informations',

    'approved_attributes' => 'Verified informations',
    'rejected_attributes' => 'Rejected informations',

    'methods_header' => 'Several methods are available:',
    'methods' => [
        'email_code' => 'Sending a code by email',
        'email_domain' => 'Providing a verified email from a french academy domain',
        'id_upload' => 'Uploading a picture of user holding their ID and a code',
        'classroom_upload' => 'Uploading a picture of teacher in his classroom with a few students or colleagues, and holding a code',
        'peer' => 'Having a verified colleague confirm their status',
        'imported_data' => 'Imported data',
        'manual' => 'Ask for a manual verification'
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

    'msg_verification_added' => 'Done! Verified informations:',

    'email_code' => [
        'step1_help' => 'Select email address.',
        'no_emails' => 'You need to edit your profile and add an email, before using the email verification.',
        'email' => 'Email',
        'code' => 'Code',
        'send_code' => 'Send code',
        'resend_code' => 'Click here to send code again.',
        'error' => 'Wrong email verification code.',
        'step2_help' => 'Input the code received on your email. If you never received it, make sure the email address provided is correct, check your spam folder, then contact us at :admin_email from that email address.',
    ],

    'email_domain' => [
        'step1_help' => 'Please provide as email from one of proposed domains. We will then send a code to this address, that you can then use to provide your status.',
        'wrong_code' => 'Wrong code',
        'account' => 'Account',
        'domain' => 'Accepted domains',
        'step2_help' => 'Please check your email <a href="mailto::email">:email</a> and enter code here.',
        'validate' => 'Validate code'
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
    ],
    
    'manual' => [
        'help' => 'Please send an email to <a href="mailto::client_email">:client_email</a>, and explain your situation. Provide the email you used for your registration in your message, and explain why you are not able to use the available automated verification system, and provide some evidence of your status or a way we can verify it. If we can check your status, we will validate it manually.'
    ]

];