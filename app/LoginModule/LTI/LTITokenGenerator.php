<?php
namespace App\LoginModule\LTI;

use App\LoginModule\Keys;
use App\LoginModule\Shared\TokenGenerator;

class LTITokenGenerator {

    public function generateToken($lti_connection) {
        $tokenParams = [
      	    'idUser' => $lti_connection->user->id,
            'sLogin' => $lti_connection->user->login,
            'lti_connection_id' => $lti_connection->id
   	    ];
        $generator = new TokenGenerator(
            config('login_module.name'),
            Keys::getPrivate()
        );
	    return $generator->generateToken($tokenParams);
    }

}