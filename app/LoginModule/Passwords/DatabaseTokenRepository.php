<?php
namespace App\LoginModule\Passwords;

use Illuminate\Auth\Passwords\DatabaseTokenRepository as DatabaseTokenRepositoryGeneric;

class DatabaseTokenRepository extends DatabaseTokenRepositoryGeneric
{

    public function createNewToken()
    {
        return substr(
            parent::createNewToken(),
            0,
            config('auth.reset_password_token_length')
        );
    }

}