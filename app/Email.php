<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Notifications\EmailVerificationNotification;

class Email extends Model implements CanResetPasswordContract
{

    use CanResetPassword, Notifiable;

    protected $fillable = [
        'user_id',
        'email',
        'role',
        'verified',
        'verification_code',
    ];

    protected $casts = [
        'verified' => 'boolean'
    ];


    protected static function boot() {
        static::saving(function($model) {
            if($model->isDirty('email')) {
                $model->verified = false;
                $model->code = str_random(10);
                $model->notify(new EmailVerificationNotification());
            }
        });
    }


    public function verifyCode($code) {
        if($this->exists() && $this->code === $code) {
            $this->verified = true;
            $this->code = null;
            return true;
        }
    }


    public function setEmailAttribute($email) {
        $this->attributes['email'] = mb_strtolower($email);
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