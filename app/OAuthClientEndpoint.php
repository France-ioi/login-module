<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OAuthClientEndpoint extends Model
{

    protected $table = 'oauth_client_endpoints';

    protected $fillable = [
        'redirect_uri'
    ];

}