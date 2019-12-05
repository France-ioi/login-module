<?php

namespace App\LoginModule\Profile;

use BootForm;

class ProfileFormElements {


    private static function verificationButton($name) {
        $url = route('verification/select_method', [
            'attribute' => $name
        ]);
        $opts = [
            'class' => 'btn-danger',
            'onclick' => 'location.href=\''.$url.'\';'
        ];
        return BootForm::addonButton(
            trans('verification.btn_verify').'  <i class="fas fa-check"></i>',
            $opts
        );
    }


    public static function text($block, $label) {
        $opts = [
            'prefix' => BootForm::addonText('Aa')
        ];
        if($block->disabled) {
            $opts[] = 'disabled';
        }
        if($block->display_verification) {
            $opts['suffix'] = self::verificationButton($block->name);
        }

        return BootForm::text(
            $block->name,
            $label,
            null,
            $opts
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
        $opts = [
            'prefix' => BootForm::addonText('Aa'),
            'autocomplete' => 'off'
        ];
        if($block->disabled) {
            $opts[] = 'disabled';
        }
        if($block->display_verification) {
            $opts['suffix'] = self::verificationButton($block->name);
        }

        return BootForm::email(
            $block->name,
            $label,
            null,
            $opts
        );
    }


    public static function verification_code($block, $label) {
        return BootForm::text(
            $block->name,
            $label
        );
    }


    public static function select($block, $label) {
        $opts = $block->disabled ? ['disabled'] : [];
        if($block->display_verification) {
            $opts['suffix'] = self::verificationButton($block->name);
        }
        return BootForm::select(
            $block->name,
            $label,
            $block->options,
            null,
            $opts
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
        $opts = [
            'prefix' => BootForm::addonText('<i class="fa-calendar-alt far"></i>')
        ];
        if($block->disabled) {
            $opts[] = 'disabled';
        }
        if($block->display_verification) {
            $opts['suffix'] = self::verificationButton($block->name);
        }
        return BootForm::text(
            $block->name,
            $label,
            null,
            $opts
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


    public static function public_info($block, $label) {
        return self::help('Information provided in other sections:').
            BootForm::staticField(
                $block->name.'_login',
                'Your login',
                '',
                ['id' => 'public_info_login']
            ).
            BootForm::staticField(
                $block->name.'_grade',
                'Your grade or graduation year',
                '',
                ['id' => 'public_info_grade']
            );
    }


    public static function public_name($block, $label) {
        return BootForm::staticField(
                $block->name.'_first_name',
                'First name',
                '',
                ['id' => 'public_name_first_name']
            ).
            BootForm::staticField(
                $block->name.'_grade',
                'Last name',
                '',
                ['id' => 'public_name_last_name']
            );
    }

}