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
            foreach($this->client->profile_fields as $field) {
                if(empty($this->user->getAttribute($field))) {
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
            'birthday'  => 'required|date_format:"Y-m-d"',
            'presentation'  => 'required',
        ];

        $required = $this->getEmpty();
        $res = [];
        foreach($required as $field) {
            $res[$field] = $all[$field];
        }

        // bugfix for laravel validation bug
        if(isset($res['primary_email']) && isset($res['secondary_phone'])) {
            $res['secondary_phone'] = $res['secondary_phone'].'|different:primary_email';
        }
        return $res;
    }

}