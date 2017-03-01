<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\LoginModule\EmailVerification\CanVerifyEmail;

class Email extends Model implements CanResetPasswordContract
{

    use CanResetPassword, CanVerifyEmail, Notifiable;

    protected $fillable = [
        'user_id',
        'email',
        'role',
        'verified'
    ];

    protected $casts = [
        'verified' => 'boolean'
    ];

    protected static function boot() {
        static::updating(function($model) {
            if($model->isDirty('email') && $model->verified) {
                $model->verified = false;
            }
        });
    }

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
