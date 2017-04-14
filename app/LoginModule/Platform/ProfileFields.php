<?php

namespace App\LoginModule\Platform;

class ProfileFields
{

    protected $client;

    protected $user;

    protected $validation;

    const VERIFICATION_FIELDS = [
        'primary_email_verified',
        'secondary_email_verified',
        'teacher_domain_verified',
    ];

    protected $fields_cache = [
        'required' => [],
        'verifiable' => [],
    ];


    public function __construct($client = null, $user = null) {
        $this->client = $client;
        $this->user = $user;
        $this->validation = new ProfileFieldsValidation($this->user);
        $this->cacheFields();
    }


    private function cacheFields() {
        if($this->client && $this->client->profile_fields) {
            $required = array_diff($this->client->profile_fields, self::VERIFICATION_FIELDS);
            $this->fields_cache['required'] = $this->validation->sortFields($required);
            $this->fields_cache['verifiable'] = array_intersect(self::VERIFICATION_FIELDS, $this->client->profile_fields);
        } else {
            $this->fields_cache['required'] = $this->validation->getFields();
        }
    }


    public function filled() {
        return count($this->getEmpty()) === 0;
    }


    public function verified() {
        if($this->user) {
            foreach($this->fields_cache['verifiable'] as $field) {
                if(!$this->user->getAttribute($field)) {
                    return false;
                }
            }
        }
        return true;
    }


    public function getVerifiable() {
        return $this->fields_cache['verifiable'];
    }


    public function getRequired() {
        return $this->fields_cache['required'];
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