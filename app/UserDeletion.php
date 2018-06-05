<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDeletion extends Model
{

    protected $fillable = [
        'user_id',
        'client_id',
    ];

}