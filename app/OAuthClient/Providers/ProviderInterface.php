<?php

    namespace App\OAuthClient\Providers;

    interface ProviderInterface {

        public function getAuthorizationURL();
        public function callback(\Illuminate\Http\Request $request);
        public function getLogoutURL(\App\User $user);

    }