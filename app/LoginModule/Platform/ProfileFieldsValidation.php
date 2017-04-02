<?php

namespace App\LoginModule\Platform;

class ProfileFieldsValidation
{

    protected $schema;

    protected $user;


    public function __construct($user = null) {
        $this->user = $user;
        $this->schema = $this->createSchema();
    }


    public function getFields() {
        return array_keys($this->schema);
    }


    public function sortFields($fields) {
        $all = array_keys($this->schema);
        return array_intersect($all, $fields);
    }



    public function getFilteredRules($required) {
        $required = array_fill_keys($required, true);
        $res = [];
        foreach($this->schema as $field => $config) {
            $res[$field] = $this->getRule($field, isset($required[$field]));
        }
        return $res;
    }


    private function getRule($field, $required = false) {
        $res = '';
        if($required && $this->schema[$field]['required']) {
            $res .= $this->schema[$field]['required'].'|';
        }
        $res .= $this->schema[$field]['valid'];
        if($this->user) {
            $res .= $this->getUniqueRuleAppendix($field);
        }
        return $res;
    }


    private function getUniqueRuleAppendix($field) {
        switch($field) {
            case 'login':
                return ',login,'.$this->user->id;
                break;
            case 'primary_email':
                if($id = $this->user->primary_email_id) {
                    return ','.$id;
                }
                break;
            case 'secondary_email':
                if($id = $this->user->secondary_email_id) {
                    return ','.$id;
                }
                break;
        }
        return '';
    }



    private function createSchema() {
        $country_codes = array_keys(trans('countries'));
        $timezones = array_keys(trans('timezones'));
        return [
            'login' => [
                'required' => 'required',
                'valid' => 'min:3|unique:users'
            ],
            'first_name' => [
                'required' => 'required',
                'valid' => 'max:100'
            ],
            'last_name' => [
                'required' => 'required',
                'valid' => 'max:100'
            ],
            'real_name_visible' => [
                'required' => false,
                'valid' => ''
            ],
            'primary_email' => [
                'required' => 'required|email',
                'valid' => 'unique:emails,email'
            ],
            'secondary_email' => [
                'required' => 'required|email',
                'valid' => 'value_different:primary_email|unique:emails,email'
            ],
            'language' => [
                'required' => 'required',
                'valid' => 'in:'.implode(',', array_keys(config('app.locales')))
            ],
            'country_code' => [
                'required' => 'required',
                'valid' => 'in:,'.implode(',', $country_codes)
            ],
            'address' => [
                'required' => 'required',
                'valid' => 'max:255'
            ],
            'city' => [
                'required' => 'required',
                'valid' => 'max:255'
            ],
            'zipcode' => [
                'required' => 'required',
                'valid' => 'max:20'
            ],
            'timezone' => [
                'required' => 'required',
                'valid' => 'in:'.implode(',', $timezones)
            ],
            'primary_phone' => [
                'required' => 'required',
                'valid' => 'max:255'
            ],
            'secondary_phone' => [
                'required' => 'required',
                'valid' => 'max:255'
            ],
            'birthday' => [
                'required' => 'required',
                'valid' => 'nullable|date_format:"Y-m-d"|before:today'
            ],
            'gender' => [
                'required' => 'required',
                'valid' => 'in:m,f'
            ],
            'presentation' => [
                'required' => 'required',
                'valid' => ''
            ],
            'website' => [
                'required' => 'required',
                'valid' => ''
            ],
            'role' => [
                'required' => 'required',
                'valid' => 'in:'.implode(',', array_keys(trans('profile.roles')))
            ],
            'ministry_of_education' => [
                'required' => 'required_if:role,teacher|required_if:country_code,'.implode(',', array_diff($country_codes, ['fr'])),
                'valid' => ''
            ],
            'ministry_of_education_fr' => [
                'required' => false,
                'valid' => ''
            ],
            'school_grade' => [
                'required' => 'required',
                'valid' => ''
            ],
            'student_id' => [
                'required' => 'required',
                'valid' => ''
            ],
            'graduation_year' => [
                'required' => 'required',
                'valid' => 'nullable|integer|between:1900,'.date('Y')
            ]
        ];
        // end of createSchema
    }


}