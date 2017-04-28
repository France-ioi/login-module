<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthConnection extends Model
{

    protected $fillable = [
        'provider',
        'uid',
        'user_id',
        'active',
        'access_token',
        'refresh_token'
    ];

    protected $visible = [
        'provider'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

}
