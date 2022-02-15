<?php

namespace App\LoginModule\Profile;

class ProfileFormRenderer {

    public static function render($schema, $user) {
        $html = '';
        $sections = config('profile.sections');
        foreach($sections as $section => $attributes) {
            $section_html = '';
            foreach($attributes as $attr) {
                if($block = $schema->block($attr)) {
                    $block_html = ProfileFormElements::{$block->type}($block, self::label($block), $user);
                    if($block->help) {
                        $block_html .= ProfileFormElements::help($block->help);
                    }
                    $section_html .= self::wrapper($block_html, $block);
                }
            }
            $html .= self::section($section, $section_html);
        }
        return $html;
    }


    private static function label($block) {
        return $block->label ?: trans('profile.'.$block->name).($block->required ? config('ui.star') : '');
    }


    private static function wrapper($html, $block) {
        $optional_field = !$block->required && !$block->recommended ? 1 : 0;
        return '<div optional_field="'.$optional_field.'" id="block_'.$block->name.'" role="block">'.$html.'</div>';
    }


    private static function section($name, $html) {
        $descriptions = trans('profile.section_descriptions');
        $description = '';
        if(isset($descriptions[$name])) {
            $description = '<div class="form-group"><p class="help-block">'.$descriptions[$name].'</p></div>';
        }
        return
            '<fieldset id="section_'.$name.'">'.
                '<legend class="sectionTitle"><i class="fas fa-'.trans('profile.icons.'.$name).' icon"></i>'.trans('profile.sections.'.$name).'</legend>'.
                $description.
                $html.
            '</fieldset>';
    }

}