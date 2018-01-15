<?php

namespace App\LoginModule\Profile;

class ProfileFormRenderer {

    public static function render($schema) {
        $html = '';
        foreach($schema->blocks() as $block) {
            $block_html = ProfileFormElements::{$block->type}($block, self::label($block));
            if($block->help) {
                $block_html .= ProfileFormElements::help($block->help);
            }
            $html .= self::wrapper($block_html, $block);
        }
        return $html;
    }


    private static function label($block) {
        return $block->label ?: trans('profile.'.$block->name).($block->required ? config('ui.star') : '');
    }


    private static function wrapper($html, $block) {
        $optional_field = !$block->required && !$block->recommended ? 1 : 0;
        return '<div optional_field="'.$optional_field.'" id="block_'.$block->name.'">'.$html.'</div>';
    }

}