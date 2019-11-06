<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\PeerVerificationNotification;

class Email extends Model implements CanResetPasswordContract
{

    use Notifiable;

    protected $fillable = [
        'user_id',
        'email',
        'role',
        'verification_code',
        'login_enabled'
    ];

    protected $casts = [
        'login_enabled' => 'bool'
    ];



    public function requireVerification() {
        $this->code = str_random(10);
        $this->save();
        $this->notify(new EmailVerificationNotification());
    }


    public function peerVerificationRequest($code) {
        $this->notify(new PeerVerificationNotification($code));
    }


    public function verifyCode($code) {
        if($this->exists() && $this->code === $code) {
            $this->code = null;
            return true;
        }
    }


    public function getEmailForPasswordReset() {
        return $this->email;
    }


    public function sendPasswordResetNotification($token) {
        $this->notify(new ResetPasswordNotification($token));
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