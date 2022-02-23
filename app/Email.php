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



    public function sendVerificationCode() {
        $this->code = str_random(10);
        $this->save();
        try {
            $this->notify(new EmailVerificationNotification());
        } catch(\Exception $e) {
            return false;
        }
        return true;
    }


    public function getCodeInputUrl() {
        return route('verification.email_code.input_code', ['role' => $this->role]).'?code='.urlencode($this->code);
    }    


    public function peerVerificationRequest($code) {
        try {
            $this->notify(new PeerVerificationNotification($code));
        } catch(\Exception $e) {
            return false;
        }
        return true;        
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
        try {
            $this->notify(new ResetPasswordNotification($token));
        } catch(\Exception $e) {
            return false;
        }
        return true;                
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