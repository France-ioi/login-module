<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ObsoletePassword extends Model
{

    protected $fillable = [
        'user_id',
        'salt',
        'password',
    ];    
    
    
    public function user() {
        return $this->belongsTo('App\User');
    }
}
