<?php

namespace App\LoginModule\Platform;

class Verificator {

    const VERIFIABLE = [
        'primary_email',
        'secondary_email',
        'teacher_domain',
    ];

    protected $fields;
    protected $user;


    public function __construct($user, $client = null) {
        $this->user = $user;
        if($client) {
            $this->fields = array_values(
                array_intersect(self::VERIFIABLE, $client->profile_fields['verified'])
            );
        } else {
            $this->fields = [];    
        }
    }    


    public function verified() {
        return count($this->errors()) == 0;
    }


    public function errors() {
        $errors = [];
        foreach($this->fields as $field) {
            if(!$this->user->getAttribute($field.'_verified')) {
                $errors[$field] = trans('verification_erorrs.'.$field);
            }            
        }
    }

}