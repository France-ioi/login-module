<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LtiConfig extends Model
{
    use \App\LoginModule\Shared\SerializesLegacyDates;

    protected $fillable = [
        'lti_consumer_key',
        'prefix'
    ];
}
