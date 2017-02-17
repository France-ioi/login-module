<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Email extends Model implements CanResetPasswordContract
{

    use CanResetPassword, Notifiable;

    protected $fillable = [
        'user_id',
        'email',
        'role',
        'confirmed'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function scopePrimary($q) {
        return $q->where('role', 'primary');
    }

    public function scopeSecondary($q) {
        return $q->where('role', 'secondary');
    }

}
