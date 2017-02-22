<?php

    return [

        'facebook' => [
            'client_id' => env('FACEBOOK_ID'),
            'client_secret' => env('FACEBOOK_SECRET')
        ],

        'google' => [
            'client_id' => env('GOOGLE_ID'),
            'client_secret' => env('GOOGLE_SECRET'),
            'openid' => [
                'realm' => env('GOOGLE_OPENID_REALM', ''),
            ]
        ],

        'pms' => [
            'client_id' => env('PMS_ID'),
            'client_secret' => env('PMS_SECRET')
        ],

    ];