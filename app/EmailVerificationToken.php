<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailVerificationToken extends Model
{

    protected $primaryKey = 'token'; // or null
    public $incrementing = false;

    protected $fillable = [
        'token',
        'email'
    ];

}
