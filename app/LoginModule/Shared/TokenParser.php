<?php
/* Copyright (c) 2013 Association France-ioi, MIT License http://opensource.org/licenses/MIT */

namespace App\LoginModule\Shared;

/**
 * Parses task tokens (RS512 JWS)
 *
 * The JWE variants (decodeJWE / decodeJWES) were removed with the PHP 8 port:
 * they were never used and relied on the abandoned spomky-labs/jose library.
 */
class TokenParser
{
   private $key;
   private $keyName;

   function __construct($key, $keyName, $keyType = null) {
      $this->key = $key;
      $this->keyName = $keyName;
   }

   /**
    * Decode JWS tokens
    */
   public function decodeJWS($tokenString)
   {
      try {
         $params = Jws::verify($tokenString, $this->key);
      } catch (\InvalidArgumentException $e) {
         throw new \Exception('Invalid token: '.$e->getMessage().', please check your SSL keys', 0, $e);
      }
      $datetime = new \DateTime();
      $datetime->modify('+1 day');
      $tomorrow = $datetime->format('d-m-Y');
      if (!isset($params['date'])) {
         if (!$params) {
            throw new \Exception('Token cannot be decrypted, please check your SSL keys');
         }
         else {
            throw new \Exception('Invalid Task token, unable to decrypt: '.json_encode($params).'; current: '.date('d-m-Y'));
         }
      }
      else if ($params['date'] != date('d-m-Y') && $params['date'] != $tomorrow) {
         throw new \Exception('API token expired');
      }

      return $params;
   }

}
