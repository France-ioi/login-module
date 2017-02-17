<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends \Laravel\Passport\Client
{

    protected $casts = [
        'profile_fields' => 'array',
        'auth_order' => 'array'
    ];

}
