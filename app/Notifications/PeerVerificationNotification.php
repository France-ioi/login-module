<?php

namespace App\Notifications;

use App\LoginModule\LocalizedNotification;

class PeerVerificationNotification extends LocalizedNotification
{

    public $code;

    public function __construct($code) {
        $this->code = $code;
    }

    public function toMail($notifiable) {
        return $this->buildMessage('peer_verification', $notifiable->user->language, [
            'code' => $notifiable->code
        ]);
    }

}