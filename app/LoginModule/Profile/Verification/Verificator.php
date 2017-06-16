<?php

namespace App\LoginModule\Profile\Verification;

use App\LoginModule\Platform\PlatformContext;

class Verificator {

    const ATTRIBUTES = [
        'primary_email_verified',
        'secondary_email_verified',
        'teacher_domain_verified'
    ];


    protected $user;
    protected $attributes;


    public function __construct(PlatformContext $context) {
        if($client = $context->client()) {
            $this->attributes = array_values(array_intersect(self::ATTRIBUTES, $client->user_attributes));
        } else {
            $this->attributes = [];
        }
    }


    public function verify($user) {
        $errors = [];
        foreach($this->attributes as $attribute) {
            if(!$user->getAttribute($attribute)) {
                $errors[$attribute] = trans('verification_errors.'.$attribute);
            }
        }
        return count($errors) > 0 ? $errors : true;
    }

}