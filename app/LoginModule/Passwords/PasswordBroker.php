<?php

namespace App\LoginModule\Passwords;

use Closure;
use Illuminate\Auth\Passwords\PasswordBroker as PasswordBrokerGeneric;

class PasswordBroker extends PasswordBrokerGeneric {

    const RESET_REFUSED = 'passwords.reset_refused';

    public function sendResetLink(array $credentials, Closure $callback = null) {
        $email = $this->getUser($credentials);
        if (is_null($email)) {
            return static::INVALID_USER;
        }

        $interval = config('auth.password_recovery_interval');
        if(!is_null($email->user->last_password_recovery_at) && $interval && time() - strtotime($email->user->last_password_recovery_at) > $interval) {
            return static::RESET_REFUSED;
        }

        $token = $this->tokens->create($email);

        if ($callback) {
            $callback($email, $token);
        } else {
            $email->sendPasswordResetNotification($token);
        }
        return static::RESET_LINK_SENT;
    }

}