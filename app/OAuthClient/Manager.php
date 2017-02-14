<?php

    namespace App\OAuthClient;

    use App\OAuthClient\Providers\FacebookProvider;
    use App\OAuthClient\Providers\GoogleProvider;
    use App\OAuthClient\Providers\PMSProvider;
    use App\OAuthClient\Providers\DefaultProvider;


    class Manager {

        static function provider($name) {
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
        }

    }