<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use App\LoginModule\Platform\BadgeApi;
use App\LoginModule\Profile\Verification\VerifiableUser;
use Carbon\Carbon;
use App\LoginModule\Graduation;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, VerifiableUser, HasRoles;


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
        'gender',
        'graduation_year',
        'graduation_grade',
        'graduation_updated_at',
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
        'primary_email_verified',
        'secondary_email_verified',
        'has_picture'
    ];

    protected $casts = [
        'admin' => 'boolean',
        'ministry_of_education_fr' => 'boolean',
        'graduation_year' => 'integer',
        'logout_config' => 'array',
        'real_name_visible' => 'boolean',
        'regular_password' => 'boolean',
        'teacher_verified' => 'boolean',
        'graduation_grade_expire_at' => 'date'
    ];


    protected static function boot() {
        static::saving(function($model) {
            if(!is_null($model->password)) {
                $model->regular_password = true;
            }
            if($model->isDirty('graduation_grade')) {
                $model->graduation_grade = Graduation::normalizeGrade($model->graduation_grade);
                if($model->graduation_grade == -1) {
                    $model->graduation_grade_expire_at = null;
                    $model->graduation_year = null;
                } else if($model->graduation_grade == -1) {
                    $model->graduation_grade_expire_at = null;
                } else if($year = Graduation::year($model)) {
                    $model->graduation_grade_expire_at = Graduation::gradeExpirationDate($model);
                    $model->graduation_year = $year;
                }
            }
            if($model->isDirty('login')) {
                $model->login_updated_at = new \DateTime;
                if($model->login_change_required && preg_match(config('profile.login_validator.new'), $model->login) == 1) {
                    $model->login_change_required = false;
                }
            }
        });

        static::deleting(function($model) {
            $badges = $model->badges()->where('do_not_possess', false)->get();
            foreach($badges as $badge) {
                if(!BadgeApi::remove($badge->url, $badge->code)) {
                    throw new \Exception('Error occured during deleting badge '.$badge->url);
                }
            }

            //tokens
        });
    }


    public function routeNotificationForMail() {
        return $this->primary_email;
    }


    public function getPrimaryEmailAttribute() {
        $primary = $this->emails()->primary()->first();
        return $primary ? $primary->email : null;
    }


    public function getPrimaryEmailIdAttribute() {
        $primary = $this->emails()->primary()->first();
        return $primary ? $primary->id : false;
    }


    public function getSecondaryEmailAttribute() {
        $secondary = $this->emails()->secondary()->first();
        return $secondary ? $secondary->email : null;
    }


    public function getSecondaryEmailIdAttribute() {
        $secondary = $this->emails()->secondary()->first();
        return $secondary ? $secondary->id : false;
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


    public function platformGroups() {
        return $this->hasMany('App\PlatformGroup');
    }


    public function tokens() {
        return $this->hasMany('\Laravel\Passport\Token');
    }


    public function originInstance() {
        return $this->belongsTo('App\OriginInstance');
    }


    public function autoLoginToken() {
        return $this->hasOne('App\AutoLoginToken');
    }


    public function verifications() {
        return $this->hasMany('App\Verification');
    }


    public function getHasPictureAttribute() {
        return (bool) $this->attributes['picture'];
    }


    public function getPictureAttribute() {
        return $this->attributes['picture'] ? $this->attributes['picture'] : asset(config('ui.profile_picture.default'));
    }

}