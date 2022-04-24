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
        'hidden_attributes',
        'attributes_filter',
        'badge_api_id',
        'api_url',
        'admin_interface_url',
        'badge_required',
        'autoapprove_authorization',
        'email'
    ];

    protected $casts = [
        // parent
        'personal_access_client' => 'bool',
        'password_client' => 'bool',
        'revoked' => 'bool',

        'user_attributes' => 'array',
        'verifiable_attributes' => 'array',
        'recommended_attributes' => 'array',
        'hidden_attributes' => 'array',
        'attributes_filter' => 'array',
        'auth_order' => 'array',
        'autoapprove_authorization' => 'boolean',
        'badge_required' => 'boolean',
    ];

    protected $attributes = [
        'auth_order' => '[]',
        'user_attributes' => '[]',
        'hidden_attributes' => '[]',
        'recommended_attributes' => '[]',
        'verifiable_attributes' => '[]',
        'attributes_filter' => '[]'
    ];


    public function verification_methods() {
        return $this->belongsToMany('App\VerificationMethod', 'oauth_client_verification_method')
            ->withPivot('expiration', 'recommended');
    }

    public function countries() {
        return $this->belongsToMany('App\Country', 'oauth_client_country');
    }

    public function official_domains() {
        return $this->hasManyThrough(
            'App\OfficialDomain',
            'App\Pivots\ClientCountry',
            'client_id',
            'country_id',
            'id',
            'country_id'
        );        
    }    


    public function badgeApi() {
        return $this->belongsTo('App\BadgeApi');
    }

}
