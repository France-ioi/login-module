<?php

namespace App\LoginModule\Platform;

class ProfileFields
{

    protected $client;
    protected $user;
    protected $validation;
    protected $fields_cache = [];
    protected $verification_fields = ['primary_email', 'secondary_email'];


    public function __construct($client = null, $user = null) {
        $this->client = $client;
        $this->user = $user;
        $this->validation = new ProfileFieldsValidation($this->user);
        $this->cacheFields();
    }


    private function cacheFields() {
        if($this->client && $this->client->profile_fields) {
            $this->fields_cache = $this->validation->sortFields($this->client->profile_fields);
        } else {
            $this->fields_cache = $this->validation->getFields();
        }
    }


    public function filled() {
        return count($this->getEmpty()) === 0;
    }


    public function verified() {
        if($this->user) {
            $fields = array_intersect($this->verification_fields, $this->fields_cache);
            foreach($fields as $field) {
                if(!$this->user->getAttribute($field.'_verified')) {
                    return false;
                }
            }
        }
        return true;
    }


    public function getRequired() {
        return $this->fields_cache;
    }


    public function getAll() {
        return $this->validation->getFields();
    }


    public function getEmpty() {
        if(!$this->client) {
            return [];
        }
        if(!$this->user) {
            return $this->getRequired();
        }
        $res = [];
        $required = $this->getRequired();
        foreach($required as $field) {
            $value = $this->user->getAttribute($field);
            if(is_null($value) || $value == '') {
                $res[] = $field;
            }
        }
        return $res;
    }


    public function getValidationRules($required) {
        return $this->validation->getFilteredRules($required);
    }

}