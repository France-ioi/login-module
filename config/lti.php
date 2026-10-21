<?php
return [
    'db' => [
        'host' => env('LTI_DB_HOST', '127.0.0.1'),
        'port' => env('LTI_DB_PORT', 3306),
        'database' => env('LTI_DB_DATABASE', 'lti-to-bebras'),
        'user' => env('LTI_DB_USERNAME', 'root'),
        'password' => env('LTI_DB_PASSWORD', 'buddy'),
    ],

    'default_login_prefix' => 'lti_',

    'prefixes_by_consumer' => [
    ],

    'send_result' => [
        'period' => 'PT600S', // DateInterval format, min period between attempts
        'attempts_max' => 5   // false for unlimited
    ]
];
