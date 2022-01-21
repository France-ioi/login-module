<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;


    protected $fillable = [
        'code',
        'name'
    ];


    public function officialDomains() {
        return $this->hasMany('App\OfficialDomain');
    }    

    public function clients()
    {
        return $this->belongsToMany('App\Client')->using('App\Pivots\ClientCountry');
    }    
}
