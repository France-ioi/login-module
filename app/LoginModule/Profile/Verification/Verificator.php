<?php
namespace App\LoginModule\Profile\Verification;

use App\LoginModule\Platform\PlatformContext;
use App\VerificationMethod;
use Carbon\Carbon;

class Verificator {

//pending, accepted, rejected
    const STATUS_NOT_VERIFIED = 'NOT_VERIFIED';
    const STATUS_VERIFICATION_REQUIRED = 'VERIFICATION_REQUIRED';
    const STATUS_IN_PROCESS = 'IN_PROCESS';
    const STATUS_ACTION_REQUIRED = 'ACTION_REQUIRED';
    const STATUS_REFRESH_REQUIRED = 'REFRESH_REQUIRED';
    const STATUS_OBSOLETE = 'OBSOLETE';
    const STATUS_VERIFIED = 'VERIFIED';


    const ATTRIBUTES = [
        'first_name',
        'last_name',
        'graduation_grade',
        'graduation_year',
        'country_code',
        'role',
        'primary_email',
        'secondary_email',
        'student_id'
    ];


    protected $user;
    protected $attributes;


    public function __construct(PlatformContext $context) {
        $this->context = $context;
        /*
        $this->attributes = [];
        if($client = $context->client()) {
            $this->attributes = array_values(array_intersect(self::ATTRIBUTES, $client->user_attributes));
        }
        */
    }



    /*
    public function verify($user) {
        $errors = [];
        foreach($this->attributes as $attribute) {
            if(!$user->getAttribute($attribute)) {
                $errors[$attribute] = trans('verification_errors.'.$attribute);
            }
        }
        return count($errors) > 0 ? $errors : true;
    }
*/

    public function status($user) {
        $client = $this->context->client();
        if(!$client) return self::STATUS_VERIFIED;

        return self::STATUS_VERIFIED;
    }


    public function clientMethods() {
        if($client = $this->context->client()) {
            return $client->verification_methods;
        }
        return VerificationMethod::all()->map(function($method) {
            $method->pivot = new \StdClass;
            $method->pivot->expiration = null;
            return $method;
        });
    }


    private function clientVerifiableAttributes() {
        if($client = $this->context->client()) {
            return $client->verifiable_attributes;
        }
        return [];
    }


    public function verificationExpired($verification, $expiration_alert = false) {
        if($verification->status != 'approved' || !$verification->approved_at) return false;
        $client = $this->context->client();
        if(!$client) return false;

        $methods = $client->verification_methods->pluck('pivot', 'id');
        if(isset($methods[$verification->method_id])) {
            $expiration = $methods[$verification->method_id]->expiration;
            if(!$expiration) {
                return false;
            }
            if($expiration_alert) {
                $expiration = max(0, $expiration - config('verification.expiration_alert_interval'));
            }
            $expire_at = Carbon::parse($verification->approved_at)->addDays($expiration);
            return Carbon::now()->gt($expire_at);
        }
        return false;
    }


    public function verificationStatus($verification) {
        if(!$verification) {
            return self::STATUS_NOT_VERIFIED;
        }
        if($this->verificationExpired($verification, true)) {
            return self::STATUS_REFRESH_REQUIRED;
        }
        if($this->verificationExpired($verification)) {
            return self::STATUS_OBSOLETE;
        }
        if($verification->status == 'pending') {
            $is_email = $verification->user_attributes[0] == 'primary_email' || $verification->user_attributes[0] == 'econdary_email';
            return $is_email ? self::STATUS_ACTION_REQUIRED : self::STATUS_IN_PROCESS;
        }
        if($verification->status == 'approved') {
            return self::STATUS_VERIFIED;
        }
        return self::STATUS_NOT_VERIFIED;
    }


    public function attributeStatus($user, $attribute) {
        $methods = $this->clientMethods();

        $verifications = $user->verifications()
            ->where('user_attribute', $attribute)
            ->whereIn('method_id', $methods->pluck('id'))
            ->get();

        // search expired
        foreach($verifications as $verification) {
            if($this->verificationExpired($verification, true)) {
                return self::STATUS_REFRESH_REQUIRED;
            }
            if($this->verificationExpired($verification)) {
                return self::STATUS_OBSOLETE;
            }
        }

        // search pending
        $statuses = $verifications->pluck('status');
        if($statuses->search('pending') !== false) {
            $is_email = $attribute == 'primary_email' || $attribute == 'econdary_email';
            return $is_email ? self::STATUS_ACTION_REQUIRED : self::STATUS_IN_PROCESS;
        }

        if($statuses->search('approved') !== false) {
            return self::STATUS_VERIFIED;
        }

        return false;
    }


    public function attributesStatus($user) {
        $verifiable = $this->clientVerifiableAttributes();
        $res = [];
        foreach(self::ATTRIBUTES as $attribute) {
            $required = in_array($attribute, $verifiable);
            $status = $this->attributeStatus($user, $attribute);
            if($status) {
                $res[$attribute] = $status;
            } else if($required) {
                $res[$attribute] = self::STATUS_VERIFICATION_REQUIRED;
            } else {
                $res[$attribute] = self::STATUS_NOT_VERIFIED;
            }
        }
        return $res;
    }


        /*
    public function attributesState($user) {
        $client = $this->context->client();
        if($client) {
            $methods = $client->verification_methods;
            $attributes = $client->verifiable_attributes;
        } else {
            $methods = VerificationMethod::all();
            $attributes = self::ATTRIBUTES;
        }

        $res = [];
        foreach($attributes as $attribute) {
            $item = new \StdClass;
            $item->name = $attribute;
            $item->verifications = [];
            foreach($methods as $method) {
                if(!in_array($attribute, $method->user_attributes)) continue;
                $v = new \StdClass;
                $v->method = $method;

                $verification = \App\Verification::where('user_id', $user->id)->where('method_id', $method->id)->first();
                $v->status = $this->verificationStatus($verification);
                $item->verifications[] = $v;
            }
            $res[] = $item;
        }
        return $res;
    }
    */



    public function verify($user) {
        $client = $this->context->client();
        if(!$client) return true;
        $verifications = $user
            ->verifications()
            ->whereIn('user_attribute', $client->verifiable_attributes)
            ->whereIn('method_id', $client->verification_methods->pluck('id'))
            ->where('status', 'approved')
            ->get();

        foreach($client->verifiable_attributes as $attribute) {
            $verified = false;
            foreach($verifications as $verification) {
                //TODO check expiration
                if($verification->user_attribute == $attribute) {
                    $verified = true;
                }
            }
            if(!$verified) return false;
        }
        return true;
    }


    public static function statusLabel($status) {
        if($status == self::STATUS_VERIFIED) {
            $c = 'label-success';
        } else if($status == self::STATUS_NOT_VERIFIED) {
            $c = 'label-warning';
        } else {
            $c = 'label-danger';
        }
        return
            '<span class="label '.$c.'">'.
            trans('verification.statuses.'.$status).
            '</span>';

    }

}