<?php
namespace App\LoginModule\Platform;

use App\LoginModule\Profile\Verification\Verification;
use App\LoginModule\Profile\UserProfile;
use App\LoginModule\Profile\ProfileFilter;

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
            $user = auth()->user();
            $profile = new UserProfile($context);
            if(!$profile->completed($user)) {
                return false;
            }
            $filter = new ProfileFilter($context);
            if(!$filter->pass($user)) {
                return false;
            }
            return true;
            /*
            $verification = new Verification($context);
            if(!$verification->authReady($user)) {
                return true;
            }
            */
        }
        if($tab_name == 'badge') {
            return (bool) $context->badge()->api();
        }
        return false;
    }

}