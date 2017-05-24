<?php

namespace App\LoginModule\Profile;

class ProfileFormRenderer {

    public static function render($schema) {
        $html = '';
        foreach($schema->blocks() as $block) {
            $html .= ProfileFormElements::{$block->type}($block, self::label($block));
        }
        return $html;
    }

    private static function label($block) {
        return trans('profile.'.$block->name).($block->required ? config('ui.star') : '');
    }

}