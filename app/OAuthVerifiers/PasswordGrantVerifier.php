<?php

namespace App\OAuthVerifiers;

class PasswordGrantVerifier
{

  public function verify($email, $password) {
      $user = \App\User::where('email', $email)->where('password', $password)->first();
      return $user ? $user->id : false;
   }

}