<?php

namespace App\Traits;

use App\Client;
use Auth;
use Request;

trait ProfileCompletion
{

    private function getProfileEmptyFields(\App\User $user) {
        dd(Request::fullUrl());
        $res = [];
        if($client = $this->getClientFromSession()) {
            foreach($client->profile_fields as $field) {
                if(empty($user->$field)) {
                    $res[] = $field;
                }
            }
        }
        return $res;
    }


    private function getClientIdFromSession() {
        dd(session()->get('url.intended'));
        $query = parse_url(session()->get('url.intended'), PHP_URL_QUERY);
        parse_str($query, $res);
        if(isset($res['client_id'])) {
            return Client::find($res['client_id']);
        }
        return null;
    }


    private function getClientIdFromUrl($url) {
        $query = parse_url($url, PHP_URL_QUERY);
        parse_str($query, $res);
        if(isset($res['client_id'])) {
            return Client::find($res['client_id']);
        }
        return null;
    }


    private function getProfileValidationRules($filter = null) {
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