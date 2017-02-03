<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{

    protected $fillable = [
        'oauth_client_id',
        'name',
        'public_key'
    ];

    public function oauth_client() {
        return $this->belongsTo('App\OAuthClient', 'oauth_client_id', 'id');
    }

}