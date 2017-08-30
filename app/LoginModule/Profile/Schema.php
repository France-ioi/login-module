<?php

namespace App\LoginModule\Profile;

use App\User;

class Schema {

    private $blocks = [];

    public function __construct(array $blocks) {
        $this->blocks = $blocks;
    }


    public function blocks() {
        return $this->blocks;
    }


    public function rules() {
        $rules = [];
        foreach($this->blocks as $block) {
            if(!$block->disabled) {
                $rules[$block->name] = $block->rule;
            }
        }
        return $rules;
    }


    public function fillableAttributes() {
        $res = [];
        foreach($this->blocks as $block) {
            if(!$block->disabled) {
                $res[] = $block->name;
            }
        }
        return $res;
    }

    public function hasRequired() {
        foreach($this->blocks as $block) {
            if($block->required) return true;
        }
        return false;
    }

}