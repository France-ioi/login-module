<?php

    namespace App\OAuthClient\Providers;

    interface ProviderInterface {

        public function getAuthorizationURL();
        public function getPreferencesURL($auth_connection);
        public function callback(\Illuminate\Http\Request $request);
        public function getLogoutURL($access_token, $redirect_url);
        public function getFixedFields();

    }
