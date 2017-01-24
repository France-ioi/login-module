<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model 
{

    protected $fillable = [
        'first_name', 
        'last_name', 
        'address',
        'zipcode',
        'city'
    ];

    protected $hidden = [
        'id',
        'user_id'
    ];
    
}