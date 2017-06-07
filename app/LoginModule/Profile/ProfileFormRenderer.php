<?php

namespace App\LoginModule\Profile;

class ProfileFormRenderer {

    public static function render($schema) {
        $html = '';
        foreach($schema->blocks() as $block) {
            $html .= ProfileFormElements::{$block->type}($block, self::label($block));
            if($block->help) {
                $html .= ProfileFormElements::help($block->help);
            }
        }
        return $html;
    }


    private static function label($block) {
        return $block->label ?: trans('profile.'.$block->name).($block->required ? config('ui.star') : '');
    }

}