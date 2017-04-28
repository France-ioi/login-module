<?php

namespace App\LoginModule\Passwords;

use Illuminate\Auth\Passwords\PasswordBrokerManager as PasswordBrokerManagerGeneric;

class PasswordBrokerManager extends PasswordBrokerManagerGeneric {

    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Password resetter [{$name}] is not defined.");
        }

        return new PasswordBroker(
            $this->createTokenRepository($config),
            $this->app['auth']->createUserProvider($config['provider'])
        );
    }

}
