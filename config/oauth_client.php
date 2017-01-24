<?php

    return [

        'facebook' => [
            'client_id' => env('FACEBOOK_ID'),
            'client_secret' => env('FACEBOOK_SECRET'),
            'redirect' => env('FACEBOOK_REDIRECT_URL'),
        ],

        'google' => [
            'client_id' => env('GOOGLE_ID'),
            'client_secret' => env('GOOGLE_SECRET'),
            'redirect' => env('GOOGLE_REDIRECT_URL'),
        ]

    ];