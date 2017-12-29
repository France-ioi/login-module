<?php
return [

    // a user should only be able to change login once per year (we may change the duration)
    // but maybe can change it if already changed less than an hour ago
    // set login_change_available = false to allow login change at any moment
    'login_change_available' => [
        'first_interval' => 'PT1H',
        'second_interval' => 'P1Y'
    ]
];