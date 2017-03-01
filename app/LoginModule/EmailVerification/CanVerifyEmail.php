<?php

namespace App\LoginModule\EmailVerification;

trait CanVerifyEmail
{

    public function sendEmailVerificationNotification($token) {
        $this->notify(new EmailVerificationNotification($token));
    }

}
