<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;


    protected $guarded = [
        'is_admin'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at'
    ];


    protected $appends = [
        'primary_email',
        'secondary_email'
    ];

    protected $casts = [
        'admin' => 'boolean',
    ];

    protected static function boot() {
        static::creating(function($model) {
            $model->last_login = new \DateTime();
        });
    }


    public function routeNotificationForMail() {
        return $this->primary_email;
    }


    public function getEmailForPasswordReset() {
        return $this->primary_email;
    }


    public function getPrimaryEmailAttribute() {
        $primary = $this->emails()->primary()->first();
        return $primary ? $primary->email : null;
    }


    public function getSecondaryEmailAttribute() {
        $secondary = $this->emails()->secondary()->first();
        return $secondary ? $secondary->email : null;
    }


    public function auth_connections() {
        return $this->hasMany('App\AuthConnection');
    }


    public function emails() {
        return $this->hasMany('App\Email');
    }


    public function badges() {
        return $this->hasMany('App\Badge');
    }

}