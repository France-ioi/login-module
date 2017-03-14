<?php

namespace App\Listeners;

use Auth;
use Session;

class LogSentMessage
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderShipped  $event
     * @return void
     */
    public function handle($event)
    {
        if(Auth::check() && Auth::user()->admin) {
            $text = preg_replace('#<style\b[^>]*>(.*?)</style>#s', '', $event->message->getBody());
            $text = strip_tags($text);
            $text = explode("\n", $text);
            $lines = [];
            foreach($text as $line) {
                $line = trim($line);
                if($line) {
                    $lines[] = $line;
                }
            }
            Session::flash('last_email_plain_text', implode("\n", $lines));
        }
    }


}