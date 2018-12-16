<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserHelperAction extends Model
{

    protected $fillable = [
        'target_user_id',
        'details'
    ];


    protected $casts = [
        'details' => 'array'
    ];

    protected $attributes = [
        'details' => '[]'
    ];

}