<?php

namespace App\LoginModule\Profile;

use BootForm;

class ProfileFormElements {


    public static function text($block, $label) {
        return BootForm::text(
            $block->name,
            $label,
            null,
            array_merge(
                $block->disabled ? ['disabled'] : [],
                [
                    'prefix' => BootForm::addonText('Aa'),
                    'suffix' => BootForm::addonButton(trans('verification.btn_verify').'  <i class="fas fa-check"></i>',
                        ['class' => 'btn-danger'])
                ]
            )
        );
    }


    public static function login($block, $label) {
        return
            self::text($block, $label).
            '<div class="alert alert-warning" id="login_change_limitations" style="display: none">'.
                trans('profile.login_change_limitations').
            '</div>';
    }


    public static function checkbox($block, $label) {
        return
            BootForm::hidden($block->name, 0).
            '<div class="checkboxSwitch">'.
                BootForm::checkbox($block->name, $label.'<span class="bg"><span class="cursor"></span></span>').
            '</div>';
    }


    public static function email($block, $label) {
        return BootForm::email(
            $block->name,
            $label,
            null,
            array_merge($block->disabled ? ['disabled'] : [], ['autocomplete' => 'off', 'prefix' => BootForm::addonText('Aa')])
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
        return BootForm::text(
            $block->name,
            $label,
            null,
            array_merge($block->disabled ? ['disabled'] : [], ['prefix' => BootForm::addonText('<i class="fa-calendar-alt far"></i>')])
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


    public static function picture($block, $label) {
        $max_file_size = config('ui.profile_picture.max_file_size');
        $opts = [
            'accept' => '.gif,.jpg,.png',
            'max_file_size' => $max_file_size
        ];
        if($block->disabled) {
            $opts[] = 'disabled';
        }
        if(request()->user()->hasPicture) {
            $url = request()->user()->picture.'?t='.strtotime(request()->user()->updated_at);
            $picture = '<img src='.$url.'\>';
        } else {
            $picture = '';
        }
        return
            '<div>'.
                $picture.
                BootForm::file(
                    $block->name,
                    $label,
                    $opts
                ).
                '<span class="help-block hidden file_size_error">'.
                    trans('profile.picture_size_error', ['size' => $max_file_size]).
                '</span>'.
            '</div>';
    }



    public static function dummy($block, $label) {
        return '';
    }


    public static function message_success($block, $label) {
        return '<div class="alert alert-success">'.$label.'</div>';
    }


    public static function message_info($block, $label) {
        return '<div class="alert alert-info">'.$label.'</div>';
    }


    public static function help($html) {
        return '<div class="form-group"><p class="help-block">'.$html.'</p></div>';
    }


    public static function teacher_domain($block, $label) {
        return BootForm::radios(
            $block->name,
            $label,
            $block->options
        ).
        '<div id="teacher_domain_alert" style="display: none">'.
            self::message_info(null, trans('profile.teacher_domain_alert', ['email' => config('mail.from.address')])).
        '</div>';
    }

}