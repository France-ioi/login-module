<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Facades\Mail;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    protected $visible = [
        'id',
        'name'
    ];

    public function profile() {
        return $this->hasOne('App\Profile');
    }

    public function oauth_connections() {
        return $this->hasMany('App\OAuthConnection');
    }

    public function sendPasswordResetNotification($token)
    {
        Mail::send('emails.password', ['token' => $token], function($m) {
            $m->from(config('mail.from.address'), config('mail.from.name'));
            $m->to($this->email, $this->name)->subject('Password reset');
        });
    }

}