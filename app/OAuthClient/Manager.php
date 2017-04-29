<?php

    namespace App\OAuthClient;

    use App\OAuthClient\Providers\FacebookProvider;
    use App\OAuthClient\Providers\GoogleProvider;
    use App\OAuthClient\Providers\PMSProvider;
    use App\OAuthClient\Providers\FranceConnectProvider;
    use App\OAuthClient\Providers\DefaultProvider;


    class Manager {

        const PROVIDERS = [
            // disabled temporary
            'facebook' => FacebookProvider::class,
            'google' => GoogleProvider::class,
            'pms' => PMSProvider::class,
            //'france_connect' => FranceConnectProvider::class
        ];

        const DEFAULT_PROVIDER = DefaultProvider::class;

        const SUPPORT_LOGOUT = ['facebook', 'google', 'france_connect']; // available at logout page

        const SUPPORT_REMOVE = ['facebook', 'google', 'france_connect']; // remove button available at auth methods page


        static function providers() {
            return array_keys(self::PROVIDERS);
        }

        static function provider($name) {
            $provider = self::PROVIDERS[$name] ? self::PROVIDERS[$name] : self::DEFAULT_PROVIDER;
            return new $provider;
        }

    }