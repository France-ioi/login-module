<?php

namespace App\LoginModule\Platform;

use App\Client;
use Auth;
use Request;
use Session;

class ProfileFields
{

    protected $client;
    protected $user;

    public function __construct($client = null, $user = null) {
        $this->client = $client;
        $this->user = $user;
    }


    public function filled() {
        return count($this->getEmpty()) === 0;
    }


    public function getRequired() {
        return $this->client ? $this->client->profile_fields : [];
    }


    public function getEmpty() {
        if(!$this->client) {
            return [];
        }
        if($this->user) {
            $res = [];
            $role = $this->user->getAttribute('role');
            $country_code = $this->user->getAttribute('country_code');
            foreach($this->client->profile_fields as $field) {
                $value = $this->user->getAttribute($field);
                if(empty($value)) {
                    if($field == 'school_grade' && $role != 'student') continue;
                    if($field == 'ministry_of_education' && $role == 'teacher' && $country_code == 'fr') continue;
                    $res[] = $field;
                }
            }
            return $res;
        }
        return $this->client->profile_fields;
    }


    public function getValidationRules() {
        $all = [
            'login' => 'required|min:3|unique:users',
            'first_name' => 'required',
            'last_name' => 'required',
            'primary_email'  => 'required|email|unique:emails,email',
            'secondary_email'  => 'required|email|unique:emails,email',
            'language' => 'required|in:'.implode(',', array_keys(config('app.locales'))),
            'country_code' => 'required|in:'.implode(',', array_keys(trans('countries'))),
            'address' => 'required',
            'city' => 'required',
            'zipcode' => 'required',
            'primary_phone' => 'required',
            'secondary_phone' => 'required',
            'role' => 'in:'.implode(',', array_keys(trans('profile.roles'))),
            'school_grade' => 'required_if:role,student',
            'ministry_of_education' => 'required_if:role,teacher',
            //'ministry_of_education_fr' => 'required_if:role,teacher|required_if:country_code,fr',
            'birthday'  => 'required|date_format:"Y-m-d"',
            'presentation'  => 'required',
        ];

        $required = $this->getEmpty();
        $res = [];
        foreach($required as $field) {
            if(isset($all[$field])) {
                $res[$field] = $all[$field];
            }
        }

        // bugfix for laravel validation bug
        if(isset($res['primary_email']) && isset($res['secondary_phone'])) {
            $res['secondary_phone'] = $res['secondary_phone'].'|different:primary_email';
        }
        return $res;
    }

}