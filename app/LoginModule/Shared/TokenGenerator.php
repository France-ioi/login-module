<?php
/* Copyright (c) 2013 Association France-ioi, MIT License http://opensource.org/licenses/MIT */

namespace App\LoginModule\Shared;

/**
 * Generates task token
 */
class TokenGenerator
{
   /**
    * Platform url.
    */
   private $platform;
   /**
    * Task private key
    */
   private $privateKey;

   function __construct($platform, $privateKey) {
      $this->platform = $platform;
      $this->privateKey = $privateKey;
   }
   /**
    * Low level token encryption function
    */
   private function encodeToken($params)
   {
      return Jws::sign($params, $this->privateKey);
   }
   /**
    * Generates the token. Expects the following parameters:
    *    => idUser
    *    => sLogin
    *    => isDefault (deprecated?)
    *    => bHasSolvedTask
    *    => nbHintsTotal
    *    => nbHintsGiven
    *    => bIsTrainer
    *    => bIsAdmin
    *    => bHasAccessCorrection
    *    => bSubmissionPossible
    *    => idTask
    *    => idChapter
    *    => sSubmissionMode (Contests, limited time, etc.)
    */
   public function generateToken($params)
   {
      $additionalParams = array(
          'date' => date('d-m-Y'),
          'platform' => urlencode($this->platform),
      );
      $sToken = $this->encodeToken(array_merge($params, $additionalParams));
      return $sToken;
   }
}
