<?php

require_once __DIR__.'/badge.php';

function removeAccount($idUser) {
   global $db;
   if (!isset($_SESSION['modules']['login']['idUser']) || $_SESSION['modules']['login']['bIsAdmin'] != 1) {
      return ['success' => false, 'error' => 'only admins can do that!'];
   }
   $badgeRemovalInfos = removeUserBadges($idUser);
   if (!$badgeRemovalInfos['success']) {
      return $badgeRemovalInfos;
   }
   $stmt = $db->prepare('delete users_auths from users_auths where idUser = :idUser;');
   $stmt->execute(['idUser' => $idUser]);
   $stmt = $db->prepare('delete users from users where id = :idUser;');
   $stmt->execute(['idUser' => $idUser]);
   return ['success' => true];
}

function generateSalt() {
   return  md5(uniqid(rand(), true));
}

function computePasswordMD5($sPassword, $sSalt) {
   return md5($sPassword.$sSalt);
}

function createAccount($db, $sLogin, $sEmail, $sOpenIdIdentity, $sPassword, $provider)
{
   //$openIdField = $provider.'_id';
   $openIdField = 'sOpenIdIdentity';
   $aValues = array(
      'sLogin' => $sLogin,
      'sEmail' => strtolower($sEmail),
      'sOpenIdIdentity' => $sOpenIdIdentity,
      'sSalt' => "",
      'sPasswordMd5' => ""
   );
   if ($aValues['sOpenIdIdentity'] == '')
   {
      //$sSalt = User::generateSalt();
      $sSalt = "";
      $aValues['sSalt'] = $sSalt;
      $aValues['sPasswordMd5'] = computePasswordMD5($sPassword, $sSalt);
   }
   if ($provider && $provider != "password") {
      $query = "INSERT INTO `users` (`sLogin`, `sEmail`, `".$openIdField."`, `sSalt`, `sPasswordMd5`, `sRegistrationDate`, `sLastLoginDate`) ".
         "VALUES (:sLogin, :sEmail, :sOpenIdIdentity, :sSalt, :sPasswordMd5, NOW(), NOW())";
   } else {
      $query = "INSERT INTO `users` (`sLogin`, `sEmail`, `sSalt`, `sPasswordMd5`, `sRegistrationDate`, `sLastLoginDate`) ".
         "VALUES (:sLogin, :sEmail, :sSalt, :sPasswordMd5, NOW(), NOW())";
      unset($aValues['sOpenIdIdentity']);
      $_SESSION['modules']['login']["hasPassword"] = true;
   }
   $db->prepare($query)->execute($aValues);
   return $db->lastInsertId();
}

function isExistingUser($db, $fieldName, $sValue) {
   $query = "SELECT `id` FROM `users` WHERE `".$fieldName."` = :sValue";
   $stmt = $db->prepare($query);
   $stmt->execute(array("sValue" => $sValue));
   return ($stmt->fetchObject() !== FALSE);
}