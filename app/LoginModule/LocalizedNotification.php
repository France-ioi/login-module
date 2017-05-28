<?php

namespace App\LoginModule;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\LoginModule\Locale;

class LocalizedNotification extends Notification
{

    public function via($notifiable) {
        return ['mail'];
    }


    public function buildMessage($localization, $language, $data) {
        $language = Locale::validate($language);
        $data = array_merge($this->defaultData(), $data);
        $subject = trans('notifications/'.$localization.'.subject', $data, $language);
        $body = trans('notifications/'.$localization.'.body', $data, $language);
        return (new MailMessage)
            ->subject($subject)
            ->markdown('notifications.layout', array_merge($data, ['body' => $body]));
    }


    public function defaultData() {
        $context = resolve(\App\LoginModule\Platform\PlatformContext::class);
        $client = $context->client();
        return [
            'app_name' => $client ? $client->name : config('app.name'),
            'app_url' => $client ? '//'.parse_url($client->redirect, PHP_URL_HOST) : config('app.url'),
        ];
    }

}