<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OfficialDomain extends Model
{
    use \App\LoginModule\Shared\SerializesLegacyDates;


    protected $fillable = [
        'country_code',
        'domain',
    ];


    public function setDomainAttribute($domain) {
        $this->attributes['domain'] = mb_strtolower($domain);
    }

}
