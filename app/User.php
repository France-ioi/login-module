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
        'email',
        'password',
        'first_name',
        'last_name',
        'gender',
        'address',
        'city',
        'zip',
        'birthday'
    ];

    protected $hidden = [
        'password'
    ];

    public function auth_connections() {
        return $this->hasMany('App\AuthConnection');
    }

    public function badges() {
        return $this->hasMany('App\Badge');
    }

    public function sendPasswordResetNotification($token)
    {
        Mail::send('emails.password', ['token' => $token], function($m) {
            $m->from(config('mail.from.address'), config('mail.from.name'));
            $name = $this->first_name.' '.$this->last_name;
            $m->to($this->email, $name)->subject('Password reset');
        });
    }

}