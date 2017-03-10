<?php

namespace App\LoginModule\Platform;

class ProfileFields
{

    protected $client;

    protected $user;

    protected $validation;

    protected $verification_fields = [
        'primary_email_verified',
        'secondary_email_verified'
    ];

    protected $role_required = [
        'school_grade',
        'ministry_of_education',
        'student_id'
    ];

    protected $fields_cache = [
        'profile' => [],
        'verification' => [],
    ];


    public function __construct($client = null, $user = null) {
        $this->client = $client;
        $this->user = $user;
        $this->validation = new ProfileFieldsValidation($this->user);
        $this->cacheFields();
    }


    private function cacheFields() {
        if($this->client && $this->client->profile_fields) {
            $required = array_diff($this->client->profile_fields, $this->verification_fields);
            if(!in_array('role', $required) && count(array_intersect($this->role_required, $required))  > 0) {
                $required[] ='role';
            }
            $this->fields_cache['required'] = $this->validation->sortFields($required);

            $this->fields_cache['verification'] = array_intersect($this->verification_fields, $this->client->profile_fields);
        }
    }


    public function filled() {
        return count($this->getEmpty()) === 0;
    }


    public function verified() {
        if($this->user) {
            foreach($this->fields_cache['verification'] as $field) {
                if(!$this->user->getAttribute($field)) {
                    return false;
                }
            }
        }
        return true;
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
        $role = $this->user->getAttribute('role');
        $country_code = $this->user->getAttribute('country_code');
        $required = $this->getRequired();
        foreach($required as $field) {
            $value = $this->user->getAttribute($field);
            if(empty($value)) {
                if(($field == 'school_grade' || $field == 'student_id') && $role && $role != 'student') continue;
                if($field == 'ministry_of_education' && $role != 'teacher') continue;
                $res[] = $field;
            }
        }
        return $res;
    }


    public function getValidationRules($required) {
        return $this->validation->getFilteredRules($required);
    }



/*
    public function getValidationRules($required_fields) {
        $login_appendix = '|unique:users';
        $primary_email_appendix = '|unique:emails,email';
        $secondary_email_appendix = '|unique:emails,email';
        if($this->user) {
            $login_appendix .= ',login,'.$this->user->id;
            if($id = $this->user->primary_email_id) {
                $primary_email_appendix .= ','.$id;
            }
            if($id = $this->user->secondary_email_id) {
                $secondary_email_appendix .= ','.$id;
            }
        }

        $all = [
            'login' => 'required|min:3'.$login_appendix,
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'primary_email'  => 'required|email'.$primary_email_appendix,
            'secondary_email'  => 'required|email|different:primary_email'.$secondary_email_appendix,
            'language' => 'required|in:'.implode(',', array_keys(config('app.locales'))),
            'country_code' => 'required|in:'.implode(',', array_keys(trans('countries'))),
            'address' => 'required|max:255',
            'city' => 'required|max:255',
            'zipcode' => 'required|max:20',
            'primary_phone' => 'required|max:255',
            'secondary_phone' => 'required|max:255',
            'birthday'  => 'required|date_format:"Y-m-d"|before:today',
            'gender' => 'required|in:m,f',
            'presentation'  => 'required',
            'website' => 'required',
            'role' => 'in:'.implode(',', array_keys(trans('profile.roles'))),
            'school_grade' => 'required_if:role,student',
            'ministry_of_education' => 'required_if:role,teacher',
            'student_id' => 'required_if:role,student',
            'graduation_year' => 'required|integer|between:1900,'.date('Y')
        ];

        $res = [];
        foreach($required_fields as $field) {
            if(isset($all[$field])) {
                $res[$field] = $all[$field];
            }
        }
        return $res;
    }


    private function extendFields($fields) {
        $role_required = ['school_grade', 'ministry_of_education', 'student_id'];
        if(!in_array('role', $fields) && count(array_intersect($role_required, $fields))  > 0) {
            array_unshift($fields, 'role');
        }
        return $fields;
    }
*/

}