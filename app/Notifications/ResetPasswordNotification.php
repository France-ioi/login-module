<?php

namespace App\Notifications;

use App\LoginModule\LocalizedNotification;

class ResetPasswordNotification extends LocalizedNotification
{

    public $token;


    public function __construct($token) {
        $this->token = $token;
    }


    public function toMail($notifiable) {
        return $this->buildMessage('reset_password', $notifiable->user->language, [
            'token' => $this->token
        ]);
    }

}
