<?php
namespace App\LoginModule\Platform;

class PlatformHelper
{

    public static function cancelUrl() {
        return \App::make(PlatformContext::class)->cancelUrl();
    }

    public static function platformName() {
        $context = \App::make(PlatformContext::class);
        return $context->client() ? $context->client()->name : trans('app.name');
    }

    public static function needBadgeVerification() {
        $context = \App::make(PlatformContext::class);
        return (bool) $context->badge()->api();
    }
}

