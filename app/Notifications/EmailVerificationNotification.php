<?php

namespace App\Notifications;

use App\LoginModule\LocalizedNotification;

class EmailVerificationNotification extends LocalizedNotification
{


    public function toMail($notifiable) {
        return $this->buildMessage('email_verification', $notifiable->user->language, [
            'link' => route('email_verification', [
                'email' => $notifiable->email,
                'code' => $notifiable->code
            ]),
            'code' => $notifiable->code
        ]);
    }

}
