<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OAuthConnection extends Model
{

    protected $table = "oauth_connections";

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