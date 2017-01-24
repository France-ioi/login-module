<?php

return [

    'defaults' => [
        'guard' => 'access_token',
        'passwords' => 'users'
    ],

    'guards' => [
        'access_token' => [
            'driver' => 'access_token',
            'provider' => 'users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => \App\User::class
        ]
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'email' => 'emails.password',
            'table' => 'password_resets',
            'expire' => 60,
        ],
    ]
];