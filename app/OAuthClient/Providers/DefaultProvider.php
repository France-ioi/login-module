<?php

    namespace App\OAuthClient\Providers;

    use App\OAuthClient\Providers\ProviderInterface;

    class DefaultProvider implements ProviderInterface {

        public function getAuthorizationURL() {
            return '/login';
        }

        public function callback(\Illuminate\Http\Request $request) {
            return null;
        }

        public function getLogoutURL(\App\User $user) {
            return '/logout'; // ??
        }

    }