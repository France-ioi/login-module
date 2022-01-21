<?php

namespace App\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ClientCountry extends Pivot
{
    
    protected $table = 'oauth_client_country';

    public function client()
    {
        return $this->belongsTo('App\Client');
    }
    
    public function country()
    {
        return $this->belongsTo('App\Country');
    }
    
    public function officialDomains()
    {
        return $this->hasManyThrough('App\OfficialDomain', 'App\Country');
    }    
}
