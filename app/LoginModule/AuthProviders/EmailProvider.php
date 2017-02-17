<?php

namespace App\LoginModule\AuthProviders;

use Illuminate\Auth\EloquentUserProvider;


class EmailProvider extends EloquentUserProvider
{

    public function retrieveByCredentials(array $credentials) {
        if(empty($credentials)) {
            return;
        }
        return $this->createModel()->newQuery()->where('email', $credentials['email'])->first();
    }

}