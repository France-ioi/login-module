<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthConnection extends Model
{

    protected $fillable = [
        'provider',
        'uid',
        'user_id'
    ];

    protected $visible = [
        'provider'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

}