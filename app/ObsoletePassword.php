<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ObsoletePassword extends Model
{
    use \App\LoginModule\Shared\SerializesLegacyDates;


    protected $fillable = [
        'user_id',
        'salt',
        'password',
        'type',
    ];    
    
    
    public function user() {
        return $this->belongsTo('App\User');
    }
}
