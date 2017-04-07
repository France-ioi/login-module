<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OfficialDomain extends Model
{

    protected $fillable = [
        'country_code',
        'domain',
    ];


}
