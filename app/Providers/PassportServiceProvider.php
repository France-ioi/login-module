<?php

namespace App\Providers;

use DateInterval;
use Laravel\Passport\PassportServiceProvider as BasePassportServiceProvider;

class PassportServiceProvider extends BasePassportServiceProvider
{
    protected function buildAuthCodeGrant()
    {
        return new \App\OAuth\AuthCodeGrant(
            $this->app->make(\Laravel\Passport\Bridge\AuthCodeRepository::class),
            $this->app->make(\Laravel\Passport\Bridge\RefreshTokenRepository::class),
            new DateInterval('PT10M')
        );
    }
}
