<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserHelperAction extends Model
{

    protected $fillable = [
        'target_user_id',
        'type',
        'hash',
        'details'
    ];


    protected $casts = [
        'details' => 'array'
    ];

    protected $attributes = [
        'details' => '[]'
    ];

}