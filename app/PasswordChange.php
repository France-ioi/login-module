<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasswordChange extends Model
{
    // Logs password changes requested by group admins

    protected $fillable = [
        'requester_user_id',
        'target_user_id'
    ];

    public function requester() {
        return $this->belongsTo('App\User', 'requester_user_id');
    }

    public function target() {
        return $this->belongsTo('App\User', 'target_user_id');
    }
}
