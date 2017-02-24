<?php

    namespace App\OAuthClient;

    use App\OAuthClient\Providers\FacebookProvider;
    use App\OAuthClient\Providers\GoogleProvider;
    use App\OAuthClient\Providers\PMSProvider;
    use App\OAuthClient\Providers\DefaultProvider;


    class Manager {

        const available_providers = [
            'facebook' => FacebookProvider::class,
            'google' => GoogleProvider::class,
            'pms' => PMSProvider::class
        ];

        const default_provider = DefaultProvider::class;


        static function providers() {
            return array_keys(self::available_providers);
        }

        static function provider($name) {
            $provider = self::available_providers[$name] ? self::available_providers[$name] : self::default_provider;
            return new $provider;
        }

    }