<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LtiConfig extends Model
{
    protected $fillable = [
        'lti_consumer_key',
        'prefix'
    ];
}
