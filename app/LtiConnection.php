<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LtiConnection extends Model
{

    protected $fillable = [
        'user_id',
        'lti_user_id',
        'lti_context_id',
        'lti_consumer_key'
    ];


    public function user() {
        return $this->belongsTo('App\User');
    }
}
