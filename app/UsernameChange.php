<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsernameChange extends Model
{
    use \App\LoginModule\Shared\SerializesLegacyDates;

    protected $fillable = ['user_id', 'old_login'];

    /**
     * Get the user that owns the username change.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
