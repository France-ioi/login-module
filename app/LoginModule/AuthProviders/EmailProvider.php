<?php

namespace App\LoginModule\AuthProviders;

use Illuminate\Auth\EloquentUserProvider;


class EmailProvider extends EloquentUserProvider
{

    public function retrieveByCredentials(array $credentials) {
        if(empty($credentials)) {
            return;
        }
        $q = $this->createModel()->newQuery();
        if(isset($credentials['email_id'])) {
            return $q->where('id', $credentials['email_id'])->first();
        } elseif (isset($credentials['email'])) {
            return $q->where('email', $credentials['email'])->where('login_enabled', true)->first();
        }
    }

}