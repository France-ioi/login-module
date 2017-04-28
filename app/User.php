<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use App\LoginModule\Platform\BadgeApi;
use App\LoginModule\TeacherEmailVerificator;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;


    protected $fillable = [
        'password',
        'login',
        'language',
        'first_name',
        'last_name',
        'real_name_visible',
        'country_code',
        'address',
        'city',
        'zipcode',
        'timezone',
        'primary_phone',
        'secondary_phone',
        'role',
        'school_grade',
        'student_id',
        'ministry_of_education',
        'ministry_of_education_fr',
        'birthday',
        'presentation',
        'picture',
        'gender',
        'graduation_year',
        'website',
        'last_login',
        'ip',
        'created_at',
        'last_password_recovery_at'
    ];

    protected $hidden = [
        'password',
        'regular_password',
        'remember_token',
        'updated_at'
    ];


    protected $appends = [
        'primary_email',
        'secondary_email',
    ];

    protected $casts = [
        'admin' => 'boolean',
        'ministry_of_education_fr' => 'boolean',
        'graduation_year' => 'integer',
        'logout_config' => 'array',
        'real_name_visible' => 'boolean',
        'regular_password' => 'boolean'
    ];


    protected static function boot() {
        static::saving(function($model) {
            if(!is_null($model->password)) {
                $model->regular_password = true;
            }
        });

        static::deleting(function($model) {
            $badges = $model->badges()->where('do_not_possess', false)->get();
            foreach($badges as $badge) {
                if(!BadgeApi::remove($badge->url, $badge->code)) {
                    throw new \Exception('Error occured during deleting badge '.$badge->url);
                }
            }
        });
    }


    public function routeNotificationForMail() {
        return $this->primary_email;
    }


    public function getPrimaryEmailAttribute() {
        $primary = $this->emails()->primary()->first();
        return $primary ? $primary->email : null;
    }


    public function getPrimaryEmailVerifiedAttribute() {
        $primary = $this->emails()->primary()->first();
        return $primary ? $primary->verified : false;
    }


    public function getPrimaryEmailIdAttribute() {
        $primary = $this->emails()->primary()->first();
        return $primary ? $primary->id : false;
    }


    public function getSecondaryEmailAttribute() {
        $secondary = $this->emails()->secondary()->first();
        return $secondary ? $secondary->email : null;
    }


    public function getSecondaryEmailVerifiedAttribute() {
        $secondary = $this->emails()->secondary()->first();
        return $secondary ? $secondary->verified : false;
    }


    public function getSecondaryEmailIdAttribute() {
        $secondary = $this->emails()->secondary()->first();
        return $secondary ? $secondary->id : false;
    }


    public function getTeacherDomainVerifiedAttribute() {
        if($this->role === 'teacher') {
            return TeacherDomainVerificator::verify($this);
        }
        return true;
    }


    public function getHasPasswordAttribute() {
        return $this->regular_password ? !is_null($this->password) : $this->obsolete_passwords()->first();
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


    public function obsolete_passwords() {
        return $this->hasMany('App\ObsoletePassword');
    }


    public function tokens() {
        return $this->hasMany('\Laravel\Passport\Token');
    }

}