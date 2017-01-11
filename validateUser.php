<?php

/* Main PHP file for server-side login, July 2014
 *
 * This file handles almost all operations on three types of login:
 *   - through login/password
 *   - through OpenID 2.0 for Google account
 *   - through Facebook PHP SDK Facebook account
 *
 * Operations are the following:
 *   - check if a session is opened
 *   - check login/password couple
 *   - create new account from user information
 *   - create new local account for users logged on Google or Facebook
 *
 * It returns a JWS token for all these operations, based on the Namshi
 * library.
 *
 * See final comments for more details.
 *
 */

// XXX is this needed?
// header("Access-Control-Allow-Origin: *");

require_once __DIR__.'/config.php';
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__."/lib/connect.php";
require_once __DIR__."/lib/session.php";
require_once __DIR__."/lib/account.php";
require_once __DIR__."/lib/loginString.php";
require_once __DIR__."/lib/strings.php";
require_once __DIR__."/lib/badge.php";
require_once __DIR__."/shared/TokenGenerator.php";

$tokenGenerator = new TokenGenerator($config->login_module->name, $config->login_module->private_key);

function requireLogged() {
   if (!isset($_SESSION) || !isset($_SESSION['modules']) || !isset($_SESSION['modules']['login']) || !isset($_SESSION['modules']['login']['idUser'])) {
      echo json_encode(array('success' => false, 'error' => 'you must be logged in to perform this action.'));
      error_log('Trying to perform action while not logged.');
      exit();
   }
}

function removeGoogle($db) {
   requireLogged();
   $query = "update `users` set `google_id`=NULL, `google_id_old`=NULL where `id`=:id";
   $stmt = $db->prepare($query);
   $stmt->execute(array('id'=>$_SESSION['modules']['login']["idUser"]));
   $_SESSION['modules']['login']['hasGoogle'] = false;
   echo json_encode(array('success' => true));
}

function removeFacebook($db) {
   requireLogged();
   $query = "update `users` set `facebook_id`=NULL where `id`=:id";
   $stmt = $db->prepare($query);
   $stmt->execute(array('id'=>$_SESSION['modules']['login']["idUser"]));
   $_SESSION['modules']['login']['hasFacebook'] = false;
   echo json_encode(array('success' => true));
}

// function handling Google or Facebook auth
function validateUserFacebook($db, $sIdentity) {
   global $tokenGenerator;
   $_SESSION['modules']['login']["identity"] = $sIdentity;
   //$query = "SELECT `id`, `sPasswordMd5`, `google_id`, `google_id_old`, `sLogin` FROM `users` WHERE `facebook_id` = :sIdentity";
   $query = "SELECT * FROM `users` WHERE `sOpenIdIdentity` = :sIdentity";
   $stmt = $db->prepare($query);
   $stmt->execute(array("sIdentity" => $sIdentity));
   if (/* not logged */!isset($_SESSION['modules']['login']['idUser'])) {
      if ($user = $stmt->fetchObject()) {
         $_SESSION['modules']['login']["idUser"] = $user->id;
         $_SESSION['modules']['login']["sLogin"] = $user->sLogin;
         $_SESSION['modules']['login']["bIsAdmin"] = $user->bIsAdmin;
         $_SESSION['modules']['login']["hasPassword"] = !!$user->sPasswordMd5;
         //$_SESSION['modules']['login']["hasGoogle"] = ($user->google_id || $user->google_id_old);
         $_SESSION['modules']['login']["hasGoogle"] = false;
         $_SESSION['modules']['login']["hasFacebook"] = true;
         $_SESSION['modules']['login']["sFirstName"] = $user->sFirstName;
         $_SESSION['modules']['login']["sSex"] = $user->sSex;
         $_SESSION['modules']['login']["sLastName"] = $user->sLastName;
         $_SESSION['modules']['login']["sStudentId"] = $user->sStudentId;
         addBadgesInSession();
         $token_params = array(
            "idUser" => $user->id,
            "sLogin" => $user->sLogin,
            "sProvider" => 'facebook',
            "sFirstName" => $user->sFirstName,
            "sSex" => $user->sSex,
            "sLastName" => $user->sLastName,
            "sStudentId" => $user->sStudentId,
            "aBadges" => $_SESSION['modules']['login']['aBadges']
         );
         $token = $tokenGenerator->generateToken($token_params);
         $db->exec('UPDATE `users` SET `sLastLoginDate`=NOW(), `sRecover` = NULL WHERE `id`='.$user->id);
         return array('login' => $user->sLogin, 'token' => $token);
      }
      return array('login' => '', 'token' => null, 'provider' => 'facebook', 'hasGoogle' => false, 'hasFacebook' => false, 'hasPassword' => false);
   } else {
      // case of a logged user: adding facebook identity if not already taken
      if ($user = $stmt->fetchObject()) {
         if ($user->id == $_SESSION['modules']['login']['idUser']) {
            $token_params = array(
               "idUser" => $user->id,
               "sLogin" => $user->sLogin,
               "sProvider" => 'facebook'
            );
            $token = $tokenGenerator->generateToken($token_params);
            return array('login' => $user->sLogin, 'token' => $token);
         }
         echo json_encode(array('success' => false, 'error' => 'error_identity_used'));
         exit();
      }
      $query = "update `users` set `facebook_id`=:sIdentity where `id`=:id";
      $stmt = $db->prepare($query);
      $stmt->execute(array('sIdentity'=>$sIdentity, 'id'=>$_SESSION['modules']['login']['idUser']));
      $_SESSION['modules']['login']['hasFacebook'] = true;
      return array('success'=>true);
   }
}

// function handling Google or Facebook auth
function validateUserGoogle($db, $sIdentity, $sOldIdentity, $email) {
   global $tokenGenerator;
   $_SESSION['modules']['login']["identity"] = $sOldIdentity;//$sIdentity
   //$query = "SELECT `id`, `sPasswordMd5`, `facebook_id`, `sLogin` FROM `users` WHERE `google_id` = :sIdentity or `google_id_old` = :sOldIdentity";
   $query = "SELECT * FROM `users` WHERE `sOpenIdIdentity` = :sIdentity or `sEmail` = :email";
   $stmt = $db->prepare($query);
   //$stmt->execute(array("sIdentity" => $sIdentity, 'sOldIdentity' => $sOldIdentity));
   $stmt->execute(array('sIdentity' => $sIdentity, 'email' => $email));
   if (/* not logged */!isset($_SESSION['modules']['login']['idUser'])) {
      if ($user = $stmt->fetchObject()) {
         $_SESSION['modules']['login']["idUser"] = $user->id;
         $_SESSION['modules']['login']["sLogin"] = $user->sLogin;
         $_SESSION['modules']['login']["bIsAdmin"] = $user->bIsAdmin;
         $_SESSION['modules']['login']["hasPassword"] = !!$user->sPasswordMd5;
         $_SESSION['modules']['login']["sFirstName"] = $user->sFirstName;
         $_SESSION['modules']['login']["sLastName"] = $user->sLastName;
         $_SESSION['modules']['login']["sSex"] = $user->sSex;
         $_SESSION['modules']['login']["sStudentId"] = $user->sStudentId;
         $_SESSION['modules']['login']["hasGoogle"] = true;
         //$_SESSION['modules']['login']["hasFacebook"] = !!$user->facebook_id;
         $_SESSION['modules']['login']["hasFacebook"] = false;
         addBadgesInSession();
         $token_params = array(
            "idUser" => $user->id,
            "sLogin" => $user->sLogin,
            "sProvider" => 'google',
            "sFirstName" => $user->sFirstName,
            "sLastName" => $user->sLastName,
            "sSex" => $user->sSex,
            "sStudentId" => $user->sStudentId
         );
         $token = $tokenGenerator->generateToken($token_params);
         $db->exec('UPDATE `users` SET `sLastLoginDate`=NOW(), `sRecover` = NULL WHERE `id`='.$user->id);
         if ($user->sOpenIdIdentity != $sIdentity) {
            $stmt = $db->prepare('UPDATE `users` SET `sOpenIdIdentity` = :sIdentity WHERE `id`= :idUser;');
            $stmt->execute(['idUser' => $user->id, 'sIdentity' => $sIdentity]);
         }
         return array('login' => $user->sLogin, 'token' => $token, 'userData' => $user, 'loginData' => $_SESSION['modules']['login']);
      }
      return array('login' => '', 'token' => null, 'provider' => 'google', 'hasGoogle' => false, 'hasFacebook' => false, 'hasPassword' => false);
   } else {
      // case of a logged user: adding google identity if not already taken
      if ($user = $stmt->fetchObject()) {
         return array('success' => false, 'error' => 'error_identity_used');
      }
      //$query = "update `users` set `google_id`=:sIdentity, `google_id_old`=:sOldIdentity where `id`=:id";
      $query = "update `users` set `sOpenIdIdentity`=:sOldIdentity where `id`=:id";
      $stmt = $db->prepare($query);
      $stmt->execute(array('sIdentity'=>$sIdentity, 'sOldIdentity'=>$sOldIdentity, 'id'=>$_SESSION['modules']['login']['idUser']));
      $_SESSION['modules']['login']['hasGoogle'] = true;
      return array('success'=>true);
   }
}

function createUser($db, $sLogin, $sEmail, $sPassword) {
   global $tokenGenerator;
   if (($sEmail == "") && isset($_SESSION['modules']['login']["sEmail"])) {
      $sEmail = $_SESSION['modules']['login']["sEmail"];
   }
   $minLengthPassword = 6;
   if (!isValidUsername($sLogin)) {
      return array("success" => false, "error" => 'error_allowed_symbols');
   }
   if (isExistingUser($db, "sLogin", $sLogin)) {
      return array("success" => false, "error" => 'error_login_used');
   }
   if ($sEmail === "") {
      $sEmail = isset($_SESSION["email"]) ? $_SESSION['modules']['login']['email'] : '';
   } else if (isExistingUser($db, "sEmail", $sEmail)) {
      return array("success" => false, "error" => 'error_email_used');
   }
   if (!isset($_SESSION['modules']['login']["identity"])) {
      if (strlen($sPassword) < $minLengthPassword) {
         return array("success" => false, "error" => 'error_password_length', 'errorArgs' => ['passwordLength' => $minLengthPassword]);
      }
      $_SESSION['modules']['login']["identity"] = "";
   }
   $_SESSION['modules']['login']["sLogin"] = $sLogin;
   if (!isset($_SESSION['modules']['login']["sProvider"]) || ! $_SESSION['modules']['login']["sProvider"]) {
      $_SESSION['modules']['login']["sProvider"] = 'password';
   }
   $_SESSION['modules']['login']["idUser"] = createAccount($db, $sLogin, $sEmail, $_SESSION['modules']['login']["identity"], $sPassword, isset($_SESSION['modules']['login']["sProvider"]) ? $_SESSION['modules']['login']["sProvider"] : null);
   $token_params = array(
      "idUser" => $_SESSION['modules']['login']["idUser"],
      "sLogin" => $sLogin,
      "sProvider" => $_SESSION['modules']['login']["sProvider"]
   );
   $token = $tokenGenerator->generateToken($token_params);
   return array("success" => true, "login" => $sLogin, 'token' => $token, 'provider' => 'password', 'loginData' => $_SESSION['modules']['login']);
}

function updateSaltAndPasswordMD5ForPassword($db, $userId, $sPassword) {
   if (!$sPassword) return;
   //$sSalt = User::generateSalt();
   $sSalt = "";
   $query = "UPDATE `users` SET `sSalt` = :sSalt, `sPasswordMd5` = :sPasswordMd5 WHERE `id` = :id";
   $db->prepare($query)->execute(array("sSalt" => $sSalt, "sPasswordMd5" => computePasswordMD5($sPassword, $sSalt), "id" => $userId));
}

function validateLoginUser($db, $sLogin, $sPassword) {
   global $tokenGenerator, $config;
   //$query = "SELECT `id`, `sLogin`, `facebook_id`, `google_id`, `google_id_old`, `sEmail`, `sPasswordMd5`, `sSalt` FROM `users` WHERE `sLogin` = :sLogin";
   $query = "SELECT * FROM `users` WHERE `sLogin` = :sLogin";
   $stmt = $db->prepare($query);
   $stmt->execute(array("sLogin" => strtolower($sLogin)));
   $user = $stmt->fetchObject();
   if (!$user) {
      echo json_encode(array("success" => false, 'error' => 'login_failed'));
      return;
   }
   // if no password or password doesn't match, and password is not generic password
   if ((empty($user->sPasswordMd5) ||
       $user->sPasswordMd5 != computePasswordMD5($sPassword, $user->sSalt)) && 
         (!$config->genericPasswordMd5 || $config->genericPasswordMd5 != computePasswordMD5($sPassword, ''))) {
      echo json_encode(array("success" => false, 'error' => 'login_failed'));
      return null;
   }
   // Update Salt if needed
   if ($user->sSalt != "") {
      updateSaltAndPasswordMD5ForPassword($db, $user->id, $sPassword);
   }
   if (isset($_SESSION['modules'])) {
      $_SESSION['modules']['login'] = array();
   } else {
      $_SESSION['modules'] = array('login' => array());
   }
   $_SESSION['modules']['login']["idUser"] = $user->id;
   $_SESSION['modules']['login']["sLogin"] = $user->sLogin;
   $_SESSION['modules']['login']["bIsAdmin"] = $user->bIsAdmin;
   $_SESSION['modules']['login']["sFirstName"] = $user->sFirstName;
   $_SESSION['modules']['login']["sLastName"] = $user->sLastName;
   $_SESSION['modules']['login']["sSex"] = $user->sSex;
   $_SESSION['modules']['login']["sStudentId"] = $user->sStudentId;
   $_SESSION['modules']['login']["sProvider"] = "password";
   $_SESSION['modules']['login']["hasPassword"] = true;
   //$_SESSION['modules']['login']["hasGoogle"] = ($user->google_id || $user->google_id_old);
   //$_SESSION['modules']['login']["hasFacebook"] = !!$user->facebook_id;
   $_SESSION['modules']['login']["hasGoogle"] = false;
   $_SESSION['modules']['login']["hasFacebook"] = false;
   addBadgesInSession();
   $token_params = array(
      //"sLanguage" => $user->sDefaultLanguage,
      "idUser" => $_SESSION['modules']['login']["idUser"],
      "sLogin" => $_SESSION['modules']['login']["sLogin"],
      "sEmail" => $user->sEmail,
      "sProvider" => $_SESSION['modules']['login']["sProvider"],
      "sFirstName" => $_SESSION['modules']['login']["sFirstName"],
      "sLastName" => $_SESSION['modules']['login']["sLastName"],
      "sSex" => $_SESSION['modules']['login']["sSex"],
      "aBadges" => $_SESSION['modules']['login']["aBadges"],
      "sStudentId" => $_SESSION['modules']['login']["sStudentId"],
    );
   $token = $tokenGenerator->generateToken($token_params);
   $db->exec('UPDATE `users` SET `sLastLoginDate`=NOW(), `sRecover` = NULL WHERE `id`='.$user->id);
   //echo json_encode(array("success" => true, "login" => $_GET["login"], 'token' => $token, 'provider' => 'password', 'hasPassword' => true, 'hasFacebook' => !!$user->facebook_id, 'hasGoogle' => ($user->google_id || $user->google_id_old)));
   echo json_encode(array("success" => true, "login" => $_GET["login"], 'token' => $token, 'provider' => 'password', 'hasPassword' => true, 'hasFacebook' => false, 'hasGoogle' =>false, 'loginData' => $_SESSION['modules']['login']));
}

if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}

function updatePassword($db, $newPassword, $oldPassword) {
   if (!isset($_SESSION) || !isset($_SESSION['modules']['login']) || !$newPassword) {
      echo json_encode(array('success' => false, 'error' => 'error_require_login', 'newPassword' => $newPassword));
      return;
   }
   if ($oldPassword) {
      $query = "SELECT `id`, `sPasswordMd5`, `sSalt` FROM `users` WHERE `id` = :id";
      $stmt = $db->prepare($query);
      $stmt->execute(array("id" => $_SESSION['modules']['login']["idUser"]));
      $user = $stmt->fetchObject();
      if (!$user) {
         echo json_encode(array("success" => false, 'error' => 'you have not been found in the database, this should not happen!'));
         return;
      }
      $computedMd5 = computePasswordMD5($oldPassword, $user->sSalt);
      if (empty($user->sPasswordMd5) || $user->sPasswordMd5 != $computedMd5) {
         echo json_encode(array("success" => false, 'error' => 'error_wrong_old_password'));
         return;
      }
   }
   if (isset($_SESSION['modules']['login']["idUserRecovered"])) {
      updateSaltAndPasswordMD5ForPassword($db, $_SESSION['modules']['login']["idUserRecovered"], $newPassword);
      $stmt = $db->prepare('update users set sRecover=NULL where id = :id;');
      $stmt->execute(array('id' => $_SESSION['modules']['login']["idUserRecovered"]));
      unset($_SESSION['modules']['login']["idUserRecovered"]);
   } else {
      updateSaltAndPasswordMD5ForPassword($db, $_SESSION['modules']['login']["idUser"], $newPassword);   
   }
   echo json_encode(array('success' => true));
}

function randomPassword() {
   $sPass = '';
   for ($i=0; $i<12; $i++) {
      $x = mt_rand(0,51);
      if ($x < 26)
         $sPass .= chr(ord('a') + $x);
      else
         $sPass .= chr(ord('A') + $x - 26);
   }
   return $sPass;
}

function customSendMail($to, $mailTitle, $mailBody) {
   global $config;
   if (!$config->email->bSendMailForReal) {
      return null;
   }
   $message = Swift_Message::newInstance();
   $message->setSubject($mailTitle);
   $message->setFrom($config->email->sEmailSender);
   $message->setTo($to);
   $message->setBody($mailBody);
   $transport = Swift_SmtpTransport::newInstance($config->email->smtpHost, $config->email->smtpPort);
   $transport->setUsername($config->email->smtpUsername);
   $transport->setPassword($config->email->smtpPassword);
   $transport->setEncryption($config->email->smtpEncryption);
   $mailer = Swift_Mailer::newInstance($transport);
   $error = '';
   $failures = [];
   try {
      $result = $mailer->send($message);
   } catch (Exception $e) {
      $error = ' '.$e->getMessage();
   }
   if ($failures) {
      $error .= 'address '.$failures[0].' was rejected';
   }
   return $error;
}

function getRecoverLink($db, $recoverEmail, $recoverLogin, $failIfNoEmail = true) {
   global $config;
   if (!$recoverLogin && !$recoverEmail) {
      echo json_encode(array('successs' => false, 'error' => 'Vous devez spécifier un login ou une adresse email'));
   }
   $query = '';
   $values = array();
   if ($recoverLogin && !$recoverEmail) {
      $query = 'select id, sLogin, sPasswordMd5, sEmail, sLastLoginDate from users where sLogin = :sLogin';
      $values['sLogin'] = $recoverLogin;
   } elseif (!$recoverLogin && $recoverEmail) {
      $query = 'select id, sLogin, sPasswordMd5, sEmail, sLastLoginDate from users where sEmail = :sEmail';
      $values['sEmail'] = $recoverEmail;
   } else {
      $query = 'select id, sLogin, sPasswordMd5, sEmail, sLastLoginDate from users where sEmail = :sEmail and sLogin = :sLogin';
      $values['sEmail'] = $recoverEmail;
      $values['sLogin'] = $recoverLogin;
   }
   $stmt = $db->prepare($query);
   $stmt->execute($values);
   $user = $stmt->fetchObject();
   if (!$user) {
      echo json_encode(array('success' => false, 'error' => 'error_unknown_user'));
      exit();
      return;
   }
   if (!$user->sPasswordMd5) {
      echo json_encode(array('success'=>false, 'error' => 'error_no_login_identity'));
      exit();
      return;
   }
   if (!$user->sEmail && $failIfNoEmail) {
      echo json_encode(array('success'=>false, 'error' => 'error_no_email'));
      exit();
      return;
   }
   // TODO: faire un check sur sLastLoginDate
   $sLogin = $user->sLogin;
   $recoverCode = md5(time().randomPassword());
   $query = 'update users set sRecover=:sRecover where id = :id;';
   $stmt = $db->prepare($query);
   $stmt->execute(array('sRecover' => $recoverCode, 'id' => $user->id));
   $link = $config->selfBaseUrl.'login.html?sLogin='.$sLogin.'&sRecover='.$recoverCode;
   return [$link, $user];
}

function recoverPassword($db, $mailTitle, $mailBody) {
   $recoverLogin = isset($_GET['recoverLogin']) ? $_GET['recoverLogin'] : null;
   list($recoverLink, $user) = getRecoverLink($db, $_GET['recoverEmail'], $recoverLogin);
   if (!$recoverLink) {
      echo json_encode(array('success' => false, 'error' => 'Impossible to generate code, this should not happen!'));
      return;
   }
   $sLogin = $user->sLogin;
   $mailBody = str_replace('{{login}}', $sLogin, $mailBody);
   $mailBody = str_replace('{{link}}', $recoverLink, $mailBody);
   $mailError = customSendMail($user->sEmail, $mailTitle, $mailBody);
   if ($mailError) {
      echo json_encode(array('success' => false, 'error' => 'Error while sending email: '.$mailError.'.'));
      return;
   }
   echo json_encode(array('success' => true));
}

function checkRecoverCode($db) {
   $recoverCode = $_GET['recoverCode'];
   $recoverLogin = $_GET['recoverLogin'];
   if (!$recoverCode || !$recoverLogin) {
      echo json_encode(array('success' => false, 'error' => 'error_missing_code_or_login'));
      return;
   }
   $query = 'select id from users where sRecover = :sRecover and sLogin = :sLogin;';
   $stmt = $db->prepare($query);
   $stmt->execute(array('sRecover'=>$recoverCode, 'sLogin'=>$recoverLogin));
   $user = $stmt->fetchObject();
   if (!$user) {
      echo json_encode(array('success' => false, 'error' => 'error_recovery_code_too_old'));
      return;
   }
   if (isset($_SESSION['modules'])) {
      $_SESSION['modules']['login'] = array();
   } else {
      $_SESSION['modules'] = array('login' => array());
   }
   $_SESSION['modules']['login']["idUserRecovered"] = $user->id;
   echo json_encode(array('success'=>true));
}

$db = connect();
//error_log(json_encode($_GET));

/*
 * Here starts the main code:
 */
if (isset($_GET['action'])) {
   if ($_GET['action'] == 'updatePassword') {
      updatePassword($db, $_GET['newPassword'], $_GET['oldPassword']);
   } else if ($_GET['action'] == 'removeGoogle') {
      removeGoogle($db);
   } else if ($_GET['action'] == 'removeFacebook') {
      removeFacebook($db);
   } else if ($_GET['action'] == 'recoverPassword') {
      $strings = getStrings(isset($_GET['language']) ? $_GET['language'] : null, isset($_GET['customStringsName']) ? $_GET['customStringsName'] : null);
      recoverPassword($db, $strings['recovery_mail_title'], $strings['recovery_mail_body']);
   } else if ($_GET['action'] == 'checkRecoverCode') {
      checkRecoverCode($db);
   }
} elseif (isset($_GET["login"])) {
   /* Authentication through login/password */
   validateLoginUser($db, $_GET["login"], $_GET["password"]);
/* Account creation with given login/password */
} else if (isset($_GET["newLogin"])) {
   echo json_encode(createUser($db, $_GET["newLogin"], $_GET["email"], $_GET["password"]));
/* Account checking (and creation) for Facebook users (not the most obvious one) */
} else if (isset($_GET["provider"]) && ($_GET["provider"] == "facebook")) {
   Facebook\FacebookSession::setDefaultApplication($config->Facebook->appId, $config->Facebook->secret);
   $helper = new Facebook\FacebookJavaScriptLoginHelper();
   $error = null;
   try {
       $session = $helper->getSession();
   } catch(Facebook\FacebookRequestException $ex) {
       $error = $ex->getMessage();
   } catch(\Exception $ex) {
       $error = $ex->getMessage();
   }
   if ($error) {
      echo json_encode(array("success" => false, 'error' => $error));
      return;
   }
   try {
      $response = (new Facebook\FacebookRequest($session, 'GET', '/me?scope=email'))->execute();
      $object = $response->getGraphObject();
      $email = $object->getProperty('email');
      $identity = 'http://www.facebook.com/'.$object->getProperty('id'); // the facebook.com prefix is to be in line with the old records in the database
   } catch (Facebook\FacebookRequestException $ex) {
      $error = $ex->getMessage();
   } catch (\Exception $ex) {
      $error = $ex->getMessage();
   }
   if ($error) {
      echo json_encode(array("success" => false, 'error' => $error));
      return;
   }
   if (isset($_SESSION['modules'])) {
      if (!isset($_SESSION['modules']['login']))
         $_SESSION['modules']['login'] = array();
   } else {
      $_SESSION['modules'] = array('login' => array());
   }
   $_SESSION['modules']['login']["sProvider"] = "facebook";
   $loginParams = validateUserFacebook($db, $identity);
   /* Do we have an account for this Facebook user ? */
   if (isset($loginParams['token']) && $loginParams['token']) {
      // Yes: return the infos
      $_SESSION['modules']['login']["sEmail"] = $email;
      echo json_encode(array("success" => true, "login" => $loginParams['login'], 'token' => $loginParams['token'], 'provider' => 'facebook', 'loginParams'=>$loginParams, 'loginData' => $_SESSION['modules']['login']));
   } elseif (isset($loginParams['success']) /* case of adding Facebook identity */) {
      echo json_encode(array("success" => $loginParams['success'], 'addingId'=>true, 'error' => $loginParams['error']));
   } else {
      // No: return an empty login
      // TODO: this is a problematic part, see TODO.txt
      $_SESSION['modules']['login']["sEmail"] = $email;
      echo json_encode(array("success" => true, 'login' => '', 'provider' => 'facebook', 'loginParams'=>$loginParams));
   }
/*
 * Authentification through Google 0Auth2.0:
 * When user successfully logs in in the popup, Google redirects the popup to
 * here (validateUser.php?code=...). So this part is called from
 * the Google popup
 *
 * See corresponding section in README.txt for more info on the use of the API.
 *
 */
} else if (isset($_GET["code"])) {
   if (isset($_SESSION['modules'])) {
      if (!isset($_SESSION['modules']['login']))
         $_SESSION['modules']['login'] = array();
   } else {
      $_SESSION['modules'] = array('login' => array());
   }
   $client = new Google_Client();
   $client->setClientId($config->Google0Auth2->client_id);
   $client->setClientSecret($config->Google0Auth2->client_secret);
   // Redirect URI must start by "http://www.france-ioi.org" in order to
   // be able to get old IDs
   $client->setRedirectUri($config->Google0Auth2->redirect_uri);
   // getting infos in OpenID format
   $client->setScopes("openid");
   $client->addScope("email");
   $validated = true;
   try {
      // authenticating code through Google REST API
      $client->authenticate($_GET['code']);
      // getting access token. Access token is a JSON string with several parts,
      // including a field "access_token" (which is confusing)
      $_SESSION['access_token'] = $client->getAccessToken();
      $id_token = $_SESSION['access_token'];
      if (gettype($id_token) == "string") {
         $id_token = json_decode($_SESSION['access_token'], true);
      }
      // id_token here is a JWT signed by Google, containing some infos. As we have
      // set our scope to "openid" and also because we have set "openid.realm" in
      // the query url (see loginManager.js), this token will contain a "openid_id"
      // field containing the old id.
      $id_token = $id_token['id_token'];
      // decoding and verifying signature of JWT token
      $token_infos = $client->verifyIdToken($id_token);
      // now we can have our old id (that was not obvious!):
      $openid_id = $token_infos['openid_id'];
      // the new id is in the "sub" field
      $sub = $token_infos['sub'];
      if(!isset($_SESSION['modules']['login']['sEmail']) || !$_SESSION['modules']['login']['sEmail']) {
         $_SESSION['modules']['login']["sEmail"] = $token_infos['email'];
      }
      $_SESSION['modules']['login']["sProvider"] = "google";
      $loginParams = validateUserGoogle($db, $sub, $openid_id, $token_infos['email']);
      if (isset($loginParams['login']) && $loginParams['login'] === "") {
         header('Location: login.html?newUser=1');
         exit();
      }
   } catch (Exception $e) {
      $validated = false;
      error_log('Login Failed: ' . $e->getMessage() . "\n");
      echo $e->getMessage();
      exit();
   }
   echo "<!DOCTYPE HTML>
      <html>
         <head>
            <script>
            var loginData = ".json_encode($_SESSION['modules']['login'], JSON_UNESCAPED_UNICODE).";
            ";
            if (isset($loginParams['sucess'])) {
               if ($loginParams['success']) {
                  echo 'opener.loginManager.googleAdded(true);';
               } else {
                  echo 'opener.loginManager.googleAdded(false, '.$loginParams['error'].');';
               }
            } else if ($validated) {
               echo "opener.loginManager.logged('".$loginParams['login']."', '".$loginParams['token']."', 'google', loginData);";
            } else {
               echo "opener.loginManager.loginFailed()";
            }
            echo "
            window.close();
            </script>
         </head>
         <body>
         </body>
      </html>";
}