<?php

namespace App\Notifications;

use App\LoginModule\LocalizedNotification;

class EmailVerificationNotification extends LocalizedNotification
{


    public function toMail($notifiable) {
        return $this->buildMessage('email_verification', $notifiable->user->language, [
            'code' => $notifiable->code,
            'url' => $notifiable->getCodeInputUrl(),
        ]);
    }

}
