<?php
namespace App\LoginModule\Platform;

use App\LoginModule\Profile\Verification\Verification;

class PlatformHelper
{

    public static function cancelUrl() {
        return \App::make(PlatformContext::class)->cancelUrl();
    }

    public static function platformName() {
        $context = \App::make(PlatformContext::class);
        return $context->client() ? $context->client()->name : trans('app.name');
    }

    public static function platformAuthorized() {
        $context = \App::make(PlatformContext::class);
        return $context->platformAuthorized();
    }


    public static function navTabVisible($tab_name) {
        if(self::platformAuthorized()) {
            return true;
        }
        if($tab_name == 'profile') {
            return true;
        }
        $context = \App::make(PlatformContext::class);
        if($tab_name == 'verification') {
            $verification = new Verification($context);
            if(!$verification->authReady(auth()->user())) {
                return true;
            }
        }
        if($tab_name == 'badge') {
            return (bool) $context->badge()->api();
        }
        return false;
    }

}