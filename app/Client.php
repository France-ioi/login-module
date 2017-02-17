<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends \Laravel\Passport\Client
{


    protected $casts = [
        'profile_fields' => 'array',
        'auth_order' => 'array'
    ];

/*
    public function setProfileFieldsAttribute(array $fields) {
        $this->attributes['profile_fields'] = json_encode($fields);
    }

    public function setProfileFieldsAttribute(array $fields) {
        $this->attributes['profile_fields'] = json_encode($fields);
    }


    public function getProfileFieldsAttribute() {
        $res = json_decode($this->attributes['profile_fields'], true);
        if(json_last_error() !== JSON_ERROR_NONE || !is_array($res)) {
            return [];
        }
        return $res;
    }
*/
}
