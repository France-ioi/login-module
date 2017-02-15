<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'language',
        'first_name',
        'last_name',
        'country_code',
        'address',
        'city',
        'zipcode',
        'primary_phone',
        'secondary_phone',
        'role',
        'birthday',
        'presentation',
        'picture'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at'
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
        return $this->emails()->where('role', 'primary')->first()->email;
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