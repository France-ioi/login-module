<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthConnection extends Model
{

    protected $fillable = [
        'provider',
        'uid',
        'user_id',
        'is_active',
        'access_token'
    ];

    protected $visible = [
        'provider'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

}
