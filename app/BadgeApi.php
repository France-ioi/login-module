<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BadgeApi extends Model
{
    protected $fillable = [
        'name',
        'url',
        'auth_enabled'
    ];

    protected $visible = [
        'id',
        'url'
    ];
}
