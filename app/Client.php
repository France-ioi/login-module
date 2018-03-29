<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends \Laravel\Passport\Client
{

    protected $fillable = [
        'name',
        'secret',
        'redirect',
        'revoked',
        'user_attributes',
        'verifiable_attributes',
        'recommended_attributes',
        //'auth_order',
        'badge_url',
        'api_url',
        'badge_required',
        'badge_autologin',
        'autoapprove_authorization'
    ];

    protected $casts = [
        // parent
        'personal_access_client' => 'bool',
        'password_client' => 'bool',
        'revoked' => 'bool',

        'user_attributes' => 'array',
        'verifiable_attributes' => 'array',
        'recommended_attributes' => 'array',
        'auth_order' => 'array',
        'autoapprove_authorization' => 'boolean',
        'badge_autologin' => 'boolean',
        'badge_required' => 'boolean',
    ];

    protected $attributes = [
        'auth_order' => '[]',
        'user_attributes' => '[]',
        'recommended_attributes' => '[]',
        'verifiable_attributes' => '[]'
    ];


    public function verification_methods() {
        return $this->belongsToMany('App\VerificationMethod', 'oauth_client_verification_method')
            ->withPivot('expiration');
    }
}
