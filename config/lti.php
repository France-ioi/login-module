<?php
return [
    'db' => [
        'host' => '127.0.0.1',
        'database' => 'lti-to-bebras',
        'user' => 'root',
        'password' => 'buddy',
    ],

    'default_login_prefix' => 'lti_',

    'prefixes_by_consumer' => [
    ],

    'send_result' => [
        'period' => 'PT600S', // DateInterval format, min period between attempts
        'attempts_max' => 5   // false for unlimited
    ]
];
