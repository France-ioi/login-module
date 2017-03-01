<?php

namespace App\LoginModule\EmailVerification;

use App\EmailVerificationToken;
use App\Email;
use Illuminate\Support\Str;

class Verificator
{


    const INVALID_TOKEN = 'invalid_token';
    const EMAIL_NOT_FOUND = 'email_not_found';
    const EMAIL_VERIFIED = 'email_verified';


    static function getToken($email) {
        if(!$verification_token = EmailVerificationToken::where('email', $email->email)->first()) {
            $verification_token = EmailVerificationToken::create([
                'email' => $email->email,
                'token' => self::createUniqueToken()
            ]);
        }
        return $verification_token->token;
    }


    static function createUniqueToken() {
        do {
            $token = Str::random(40);
        } while(EmailVerificationToken::find($token));
        return $token;
    }


    static function verifyEmail($token) {
        $res = self::INVALID_TOKEN;
        if($token && $validation_token = EmailVerificationToken::find($token)) {
            if($email = Email::where('email', $validation_token->email)->first()) {
                $email->verified = true;
                $email->save();
                $res = self::EMAIL_VERIFIED;
            } else {
                $res = self::EMAIL_NOT_FOUND;
            }
            $validation_token->delete();
        }
        return $res;
    }

}