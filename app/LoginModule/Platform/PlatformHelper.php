<?php
namespace App\LoginModule\Platform;

class PlatformHelper
{

    public static function cancelUrl() {
        return \App::make(PlatformContext::class)->cancelUrl();
    }
}

