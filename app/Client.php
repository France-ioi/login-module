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
        //return $this->hasMany('App\OfficialDomain')->using('App\Pivots\ClientCountry');
        return $this->hasManyThrough(
            'App\OfficialDomain',          // The model to access to
            'App\Pivots\ClientCountry', // The intermediate table that connects the User with the Podcast.
            'client_id',                 // The column of the intermediate table that connects to this model by its ID.
            'country_id',              // The column of the intermediate table that connects the Podcast by its ID.
            'id',                      // The column that connects this model with the intermediate model table.
            'country_id'               // The column of the Audio Files table that ties it to the Podcast.
        );        
    }    

    public function badgeApi() {
        return $this->belongsTo('App\BadgeApi');
    }


    public function makeURL($path) {
        $p = parse_url($this->redirect);
        return $p['scheme'].'://'.$p['host'].'/'.ltrim($path, '/');
    }
}
