<?php
namespace App\LoginModule\Profile;

use App\LoginModule\Platform\PlatformContext;


class ProfileFilter {

    protected $context;


    public function __construct(PlatformContext $context) {
        $this->context = $context;
    }


    public function data() {
        if($client = $this->context->client()) {
            return $client->attributes_filter;
        }
        return [];
    }


    public function pass($user) {
        return count($this->rejectedAttributes($user)) === 0;
    }


    public function rejectedAttributes($user) {
        $data = $this->data();
        $attributes = $user->getAttributes();
        $res = [];
        foreach($data as $attr => $value) {
            if(isset($attributes[$attr]) && $attributes[$attr] !== $value) {
                $res[$attr] = [
                    'current_value' => $attributes[$attr],
                    'required_value' => $value
                ];
            }
        }
        return $res;
    }

}