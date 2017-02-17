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

    public function __construct(\App\Client $client, \App\User $user) {
        $this->client = $client;
        $this->user = $user;
    }


    public function filled() {
        return $this->getEmpty() === 0;
    }


    public function getRequired() {
        return $this->client->profile_fields;
    }


    public function getEmpty() {
        $res = [];
        if($this->client) {
            foreach($this->client->profile_fields as $field) {
                if(empty($user->$field)) {
                    $res[] = $field;
                }
            }
        }
        return $res;
    }


    public function getValidationRules($filter = null) {
        $res = [
            'login' => 'required|min:3|unique:users',
            'language' => 'required|in:'.array_keys(config('app.locales')),
            'first_name' => 'required',
            'last_name' => 'required',
            'country_code' => 'required|in:'.array_keys(config('countries')),
            'address' => 'required',
            'city' => 'required',
            'zipcode' => 'required',
            'primary_phone' => 'required',
            'secondary_phone' => 'required',
            'role' => 'in:student,teacher,other',
            'birthday'  => 'required|date',
            'presentation'  => 'required',
            'primary_email'  => 'required|email|unique:emails',
            'secondary_email'  => 'required|email|different:primary_email|unique:emails',
        ];

        if($filter) {
            foreach($filter as $field) {
                unset($res[$field]);
            }
        }
        return $res;
    }

}