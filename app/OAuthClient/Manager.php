<?php

    namespace App\OAuthClient;

    use App\OAuthClient\Providers\FacebookProvider;
    use App\OAuthClient\Providers\GoogleProvider;
    use App\OAuthClient\Providers\PMSProvider;
    use App\OAuthClient\Providers\DefaultProvider;


    class Manager {

        const providers = [
            'facebook' => FacebookProvider::class,
            'google' => GoogleProvider::class,
            'pms' => PMSProvider::class
        ];

        const default_provider = DefaultProvider::class;


        static function list() {
            return array_keys(self::providers);
        }


        static function provider($name) {
            $provider = isset(self::providers[$name]) ? self::providers[$name] : self::default_provider;
            return new $provider;
            /*
            switch($name) {
                case 'facebook':
                    return new FacebookProvider();
                    break;
                case 'google':
                    return new GoogleProvider();
                    break;
                case 'pms':
                    return new PMSProvider();
                    break;
                default:
                    return new DefaultProvider();
                    break;
            }
            */
        }

    }