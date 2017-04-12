<?php

    namespace App\OAuthClient\Providers;

    use App\OAuthClient\Providers\ProviderInterface;

    class DefaultProvider implements ProviderInterface {

        public function getAuthorizationURL() {
            return '/login';
        }

        public function getPreferencesURL() {
            return '/profile';
        }

        public function callback(\Illuminate\Http\Request $request) {
            return null;
        }

        public function getLogoutURL($access_token, $redirect_url) {
            return '/logout'; // ??
        }

        public function getFixedFields() {
            return [];
        }

    }
