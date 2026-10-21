<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Moved from config/app.php ('log' and 'log_level') with the Laravel 12
    | upgrade. The legacy APP_LOG and APP_LOG_LEVEL variables are still read.
    |
    | Available channels: "single", "daily", "syslog", "errorlog"
    |
    */

    'default' => env('LOG_CHANNEL', env('APP_LOG', 'single')),

    'channels' => [
        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', env('APP_LOG_LEVEL', 'debug')),
            'replace_placeholders' => true,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', env('APP_LOG_LEVEL', 'debug')),
            'days' => env('LOG_DAILY_DAYS', 5),
            'replace_placeholders' => true,
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', env('APP_LOG_LEVEL', 'debug')),
            'replace_placeholders' => true,
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', env('APP_LOG_LEVEL', 'debug')),
            'replace_placeholders' => true,
        ],
    ],

];
