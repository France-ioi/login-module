<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutoLoginToken extends Model
{
    use \App\LoginModule\Shared\SerializesLegacyDates;


    protected $fillable = [
        'user_id',
        'token'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

}
