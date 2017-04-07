<?php

    namespace App\OAuthClient\Providers;

    interface ProviderInterface {

        public function getAuthorizationURL();
        public function getPreferencesURL();
        public function callback(\Illuminate\Http\Request $request);
        public function getLogoutURL($access_token, $redirect_url);

    }
