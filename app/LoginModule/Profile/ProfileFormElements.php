<?php

namespace App\LoginModule\Profile;

use BootForm;

class ProfileFormElements {

    public static function text($block, $label) {
        return BootForm::text(
            $block->name,
            $label,
            null,
            $block->disabled ? ['disabled'] : []
        );
    }


    public static function checkbox($block, $label) {
        return
            BootForm::hidden($block->name, 0).
            BootForm::checkbox($block->name, $label);
    }


    public static function email($block, $label) {
        return BootForm::email(
            $block->name,
            $label,
            null,
            $block->disabled ? ['disabled'] : []
        );
    }


    public static function verification_code($block, $label) {
        return BootForm::text(
            $block->name,
            $label
        );
    }


    public static function select($block, $label) {
        return BootForm::select(
            $block->name,
            $label,
            $block->options,
            null,
            $block->disabled ? ['disabled'] : []
        );
    }


    public static function radios($block, $label) {
        return BootForm::radios(
            $block->name,
            $label,
            $block->options,
            null,
            null,
            $block->disabled ? ['disabled'] : []
        );
    }


    public static function date($block, $label) {
        return BootForm::date(
            $block->name,
            $label,
            null,
            $block->disabled ? ['disabled'] : []
        );
    }


    public static function textarea($block, $label) {
        return BootForm::textarea(
            $block->name,
            $label,
            null,
            $block->disabled ? ['disabled'] : []
        );
    }


    public static function dummy($block, $label) {
        return '';
    }


    public static function message_success($block, $label) {
        return '<div class="alert alert-success">'.$label.'</div>';
    }


    public static function help($html) {
        return '<div class="form-group"><p class="help-block">'.$html.'</p></div>';
    }

}