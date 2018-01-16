<?php

namespace App\LoginModule;

use App\OAuthClient\Manager;

class AuthList {

    // default list of all available auth methods
    public function all() {
        return array_merge(
            ['login', 'badge'],
            Manager::providers(),
            ['_']
        );
    }


    public function split($methods) {
        $methods = $this->normalize($methods);

        $res = [
            'visible' => [],
            'hidden' => []
        ];
        $target = 'visible';
        foreach($methods as $method) {
            if($method == '_') {
                $target = 'hidden';
                continue;
            }
            $res[$target][] = $method;
        }
        return $res;
    }


    public function normalize($methods) {
        $all = $this->all();
        if(!is_array($methods)) {
            return $all;
        }
        // remove non existent methods
        $methods = array_values(array_intersect($methods, $all));
        // add missed methods
        $methods = array_merge($methods, array_diff($all, $methods));
        return $methods;
    }


}