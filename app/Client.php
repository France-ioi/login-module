<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends \Laravel\Passport\Client
{

    protected $casts = [
        'user_attributes' => 'array',
        'auth_order' => 'array',
        'autoapprove_authorization' => 'boolean',
        'badge_autologin' => 'boolean',
    ];

    protected $attributes = [
        'auth_order' => '[]',
        'user_attributes' => '[]',
    ];

}
