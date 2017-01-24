<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OAuthClient extends Model
{

    protected $table = 'oauth_clients';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'secret'
    ];

    protected $visible = [
        'id',
        'name'
    ];

    public function oauth_client_endpoint() {
        return $this->hasOne('App\OAuthClientEndpoint', 'client_id');
    }

}