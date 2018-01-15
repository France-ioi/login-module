<?php

namespace App\LoginModule\Profile;

use \Illuminate\Validation\Rule;

class SchemaConfig {

    public static function login($user = null) {
        $valid = ['min:3'];
        if(!($user && $user->login === request()->get('login'))) {
            $valid[] = 'login';
        }
        if($user) {
            $valid[] = Rule::unique('users')->ignore($user->id);
        } else {
            $valid[] = Rule::unique('users');
        }

        return [
            'type' => 'login',
            'required' => 'required',
            'valid' => $valid
        ];
    }


    public static function first_name($user = null) {
        return [
            'type' => 'text',
            'required' => 'required',
            'valid' => 'max:100'
        ];
    }


    public static function last_name($user = null) {
        return [
            'type' => 'text',
            'required' => 'required',
            'valid' => 'max:100'
        ];
    }


    public static function real_name_visible($user = null) {
        return [
            'type' => 'checkbox'
        ];
    }


    public static function country_code($user = null) {
        $options = trans('countries');
        return [
            'type' => 'select',
            'options' => ['' => '...'] + $options,
            'required' => 'required',
            'valid' => 'in:,'.implode(',', array_keys($options))
        ];
    }


    public static function role($user = null) {
        $options = trans('profile.roles');
            return [
                'type' => 'select',
                'options' => $options,
                'required' => 'required',
                'valid' => 'in:'.implode(',', array_keys($options))
            ];
    }


    public static function teacher_domain_verified($user = null) {
        if($user && $user->role == 'teacher' && $user->teacher_domain_verified) {
            return [
                'type' => 'dummy'
            ];
        }
        return [
            'type' => 'teacher_domain',
            'required' => false,
            'options' => trans('profile.teacher_domain_options')
        ];
    }


    public static function primary_email($user = null) {
        if($user && $user->primary_email_id) {
            $valid = Rule::unique('emails', 'email')->ignore($user->primary_email_id);
        } else {
            $valid = Rule::unique('emails', 'email');
        }
        return [
            'type' => 'email',
            'required' => ['required', 'email'],
            'valid' => [$valid]
            //'unique:emails,email'.($user && $user->primary_email_id ? ','.$user->primary_email_id : '')
        ];
    }


    public static function primary_email_verified($user = null) {
        if($user && $user->primary_email_id && $user->primary_email_verified) {
            return [
                'type' => 'message_success',
                'label' => trans('profile.email_verified')
            ];
        }
        return [
            'type' => 'text',
            'name' => 'primary_email_verification_code',
            //'required' => 'required',
            'help' => trans('profile.email_verification_help', [
                'email' => '<a href="mailto:'.config('mail.from.address').'">'.config('mail.from.address').'</a>'
            ])
        ];
    }


    public static function secondary_email($user = null) {
        if($user && $user->secondary_email_id) {
            $valid = Rule::unique('emails', 'email')->ignore($user->secondary_email_id);
        } else {
            $valid = Rule::unique('emails', 'email');
        }
        return [
            'type' => 'email',
            'required' => ['required', 'email'],
            'valid' => [
                'value_different:primary_email',
                $valid
                //'unique:emails,email'.($user && $user->secondary_email_id ? ','.$user->secondary_email_id : '')
            ]
        ];
    }


    public static function secondary_email_verified($user = null) {
        if($user && $user->secondary_email_id && $user->secondary_email_verified) {
            return [
                'type' => 'message_success',
                'label' => trans('profile.email_verified')
            ];
        }
        return [
            'type' => 'text',
            'name' => 'secondary_email_verification_code',
            //'required' => 'required',
            'help' => trans('profile.email_verification_help', [
                'email' => '<a href="mailto:'.config('mail.from.address').'">'.config('mail.from.address').'</a>'
            ])
        ];
    }


    public static function language($user = null) {
        $options = config('app.locales');
        return [
            'type' => 'select',
            'options' => $options,
            'required' => 'required',
            'valid' => 'in:'.implode(',', array_keys($options)),
        ];
    }





    public static function address($user = null) {
        return [
            'type' => 'text',
            'required' => 'required',
            'valid' => 'max:255'
        ];
    }


    public static function city($user = null) {
        return [
            'type' => 'text',
            'required' => 'required',
            'valid' => 'max:255'
        ];
    }


    public static function zipcode($user = null) {
        return [
            'type' => 'text',
            'required' => 'required',
            'valid' => 'max:20'
        ];
    }


    public static function timezone($user = null) {
        $options = trans('timezones');
        return [
            'type' => 'select',
            'options' => ['' => '...'] + $options,
            'required' => 'required',
            'valid' => 'in:,'.implode(',', array_keys($options))
        ];
    }


    public static function primary_phone($user = null) {
        return [
            'type' => 'text',
            'required' => 'required',
            'valid' => 'max:255'
        ];
    }


    public static function secondary_phone($user = null) {
        return [
            'type' => 'text',
            'required' => 'required',
            'valid' => 'max:255'
        ];
    }


    public static function birthday($user = null) {
        return [
            'type' => 'date',
            'required' => 'required',
            'valid' => [
                'nullable',
                'date_format:"Y-m-d"',
                'before:today'
            ]
        ];
    }


    public static function gender($user = null) {
        $options = trans('profile.genders');
        return [
            'type' => 'radios',
            'options' => $options,
            'required' => 'required',
            'valid' => 'in:'.implode(',', array_keys($options))
        ];
    }


    public static function presentation($user = null) {
        return [
            'type' => 'textarea',
            'required' => 'required'
        ];
    }


    public static function website($user = null) {
        return [
            'type' => 'text',
            'required' => 'required'
        ];
    }



    /*
    Disabled temporarily
    public static function ministry_of_education($user = null) {
        $country_codes = array_keys(trans('countries'));
        return [
            'type' => 'checkbox',
            'required' => 'required_if:role,teacher|required_if:country_code,'.implode(',', array_diff($country_codes, ['fr']))
        ];
    }


    public static function ministry_of_education_fr($user = null) {
        return [
            'type' => 'checkbox'
        ];
    }


    public static function school_grade($user = null) {
        return [
            'type' => 'text',
            'required' => 'required'
        ];
    }
*/

    public static function student_id($user = null) {
        return [
            'type' => 'text',
            'required' => 'required'
        ];
    }


    public static function graduation_year($user = null) {
        return [
            'type' => 'text',
            //'required' => 'required',
            'valid' => [
                'nullable',
                'integer',
                'between:1900,2100'
            ],
            'prepend' => [
                'graduation_grade'
            ]
        ];
    }


    public static function graduation_grade($user = null) {
        $options = trans('graduation_grades');
        $date = \App\LoginModule\Graduation::gradeExpirationDate($user);
        return [
            'type' => 'select',
            'options' => ['' => '...'] + $options,
            //'required' => 'required',
            'valid' => [
                'nullable',
                'in:'.implode(',', array_keys($options))
            ],
            'label' => trans('profile.graduation_grade', [
                'year_begin' => $date->year - 1,
                'year_end' => $date->year
            ])
        ];
    }


    public static function picture($user = null) {
        return [
            'type' => 'picture',
            //'required' => 'required',
            'valid' => [
                'image',
                'max:'.(1024*config('ui.profile_picture.max_file_size'))
            ]
        ];
    }



}