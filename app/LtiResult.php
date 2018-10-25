<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LtiResult extends Model
{
    protected $fillable = [
        'lti_connection_id',
        'score',
        'attempts',
        'last_attempt'
    ];


    protected static function boot() {
        static::creating(function($model) {
            $model->attempts = 0;
            $model->last_attempt = new \DateTime;
        });
    }

}
