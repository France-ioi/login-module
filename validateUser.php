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
require_once __DIR__."/translate.inc.php";
require_once __DIR__."/shared/TokenGenerator.php";

$tokenGenerator = new TokenGenerator($config->login_module->name, $config->login_module->private_key);

function isValidUsername($sName) {
   return preg_match("/^[a-z0-9_\.-]{3,15}$/", $sName);
}

function isExistingUser($db, $fieldName, $sValue) {
   $query = "SELECT `id` FROM `users` WHERE `".$fieldName."` = :sValue";
   $stmt = $db->prepare($query);
   $stmt->execute(array("sValue" => $sValue));
   return ($stmt->fetchObject() !== FALSE);
}

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
   $query = "SELECT `id`, `sPasswordMd5`, `sOpenIdIdentity`, `sLogin` FROM `users` WHERE `sOpenIdIdentity` = :sIdentity";
   $stmt = $db->prepare($query);
   $stmt->execute(array("sIdentity" => $sIdentity));
   if (/* not logged */!isset($_SESSION['modules']['login']['idUser'])) {
      if ($user = $stmt->fetchObject()) {
         $_SESSION['modules']['login']["idUser"] = $user->id;
         $_SESSION['modules']['login']["sLogin"] = $user->sLogin;
         $_SESSION['modules']['login']["hasPassword"] = !!$user->sPasswordMd5;
         //$_SESSION['modules']['login']["hasGoogle"] = ($user->google_id || $user->google_id_old);
         $_SESSION['modules']['login']["hasGoogle"] = false;
         $_SESSION['modules']['login']["hasFacebook"] = true;
         $token_params = array(
            "idUser" => $user->id,
            "sLogin" => $user->sLogin,
            "sProvider" => 'facebook'
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
         echo json_encode(array('success' => false, 'error' => 'Cette identité est déjà associée à un utilisateur.'));
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
function validateUserGoogle($db, $sIdentity, $sOldIdentity) {
   global $tokenGenerator;
   $_SESSION['modules']['login']["identity"] = $sOldIdentity;//$sIdentity
   //$query = "SELECT `id`, `sPasswordMd5`, `facebook_id`, `sLogin` FROM `users` WHERE `google_id` = :sIdentity or `google_id_old` = :sOldIdentity";
   $query = "SELECT `id`, `sPasswordMd5`, `sOpenIdIdentity`, `sLogin` FROM `users` WHERE `sOpenIdIdentity` = :sOldIdentity";
   $stmt = $db->prepare($query);
   //$stmt->execute(array("sIdentity" => $sIdentity, 'sOldIdentity' => $sOldIdentity));
   $stmt->execute(array('sOldIdentity' => $sOldIdentity));
   if (/* not logged */!isset($_SESSION['modules']['login']['idUser'])) {
      if ($user = $stmt->fetchObject()) {
         $_SESSION['modules']['login']["idUser"] = $user->id;
         $_SESSION['modules']['login']["sLogin"] = $user->sLogin;
         $_SESSION['modules']['login']["hasPassword"] = !!$user->sPasswordMd5;
         $_SESSION['modules']['login']["hasGoogle"] = true;
         //$_SESSION['modules']['login']["hasFacebook"] = !!$user->facebook_id;
         $_SESSION['modules']['login']["hasFacebook"] = false;
         $token_params = array(
            "idUser" => $user->id,
            "sLogin" => $user->sLogin,
            "sProvider" => 'google'
         );
         $token = $tokenGenerator->generateToken($token_params);
         $db->exec('UPDATE `users` SET `sLastLoginDate`=NOW(), `sRecover` = NULL WHERE `id`='.$user->id);
         return array('login' => $user->sLogin, 'token' => $token);
      }
      return array('login' => '', 'token' => null, 'provider' => 'google', 'hasGoogle' => false, 'hasFacebook' => false, 'hasPassword' => false);
   } else {
      // case of a logged user: adding google identity if not already taken
      if ($user = $stmt->fetchObject()) {
         return array('success' => false, 'error' => 'Cette identité Google est déjà associée à un autre utilisateur.');
      }
      //$query = "update `users` set `google_id`=:sIdentity, `google_id_old`=:sOldIdentity where `id`=:id";
      $query = "update `users` set `sOpenIdIdentity`=:sOldIdentity where `id`=:id";
      $stmt = $db->prepare($query);
      $stmt->execute(array('sIdentity'=>$sIdentity, 'sOldIdentity'=>$sOldIdentity, 'id'=>$_SESSION['modules']['login']['idUser']));
      $_SESSION['modules']['login']['hasGoogle'] = true;
      return array('success'=>true);
   }
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

function generateSalt() {
   return  md5(uniqid(rand(), true));
}

function computePasswordMD5($sPassword, $sSalt) {
   return md5($sPassword.$sSalt);
}

function createUser($db, $sLogin, $sEmail, $sPassword) {
   global $tokenGenerator;
   if (($sEmail == "") && isset($_SESSION['modules']['login']["sEmail"])) {
      $sEmail = $_SESSION['modules']['login']["sEmail"];
   }
   $minLengthPassword = 6;
   if (!isValidUsername($sLogin)) {
      return array("success" => false, "error" => translate::t("TextAllowedSymbols"));
   }
   if (isExistingUser($db, "sLogin", $sLogin)) {
      return array("success" => false, "error" => translate::t("LoginAlreadyUsed"));
   }
   if ($sEmail === "") {
      $sEmail = isset($_SESSION["email"]) ? $_SESSION['modules']['login']['email'] : '';
   } else if (isExistingUser($db, "sEmail", $sEmail)) {
      return array("success" => false, "error" => translate::t("EmailAlreadyUsed"));
   }
   if (!isset($_SESSION['modules']['login']["identity"])) {
      if (strlen($sPassword) < $minLengthPassword) {
         return array("success" => false, "error" => translate::tParam("PasswordTooShort", $minLengthPassword));
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
   return array("success" => true, "login" => $sLogin, 'token' => $token, 'provider' => 'password');
}

function updateSaltAndPasswordMD5ForPassword($db, $userId, $sPassword) {
   //$sSalt = User::generateSalt();
   $sSalt = "";
   $query = "UPDATE `users` SET `sSalt` = :sSalt, `sPasswordMd5` = :sPasswordMd5 WHERE `id` = :id";
   $db->prepare($query)->execute(array("sSalt" => $sSalt, "sPasswordMd5" => computePasswordMD5($sPassword, $sSalt), "id" => $userId));
}

function validateLoginUser($db, $sLogin, $sPassword) {
   global $tokenGenerator;
   //$query = "SELECT `id`, `sLogin`, `facebook_id`, `google_id`, `google_id_old`, `sEmail`, `sPasswordMd5`, `sSalt` FROM `users` WHERE `sLogin` = :sLogin";
   $query = "SELECT `id`, `sLogin`, `sOpenIdIdentity`, `sEmail`, `sPasswordMd5`, `sSalt` FROM `users` WHERE `sLogin` = :sLogin";
   $stmt = $db->prepare($query);
   $stmt->execute(array("sLogin" => strtolower($sLogin)));
   $user = $stmt->fetchObject();
   if (!$user) {
      echo json_encode(array("success" => false, 'error' => 'user not in database'));
      return;
   }
   if (empty($user->sPasswordMd5) ||
       ($user->sPasswordMd5 != computePasswordMD5($sPassword, $user->sSalt))) {
      echo json_encode(array("success" => false, 'error' => 'wrong password'));
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
   error_log("User : ".json_encode($user)." for login : ".$sLogin);
   $_SESSION['modules']['login']["sLogin"] = $user->sLogin;
   $_SESSION['modules']['login']["sProvider"] = "password";
   $_SESSION['modules']['login']["hasPassword"] = true;
   //$_SESSION['modules']['login']["hasGoogle"] = ($user->google_id || $user->google_id_old);
   //$_SESSION['modules']['login']["hasFacebook"] = !!$user->facebook_id;
   $_SESSION['modules']['login']["hasGoogle"] = false;
   $_SESSION['modules']['login']["hasFacebook"] = false;
   $token_params = array(
      //"sLanguage" => $user->sDefaultLanguage,
      "idUser" => $_SESSION['modules']['login']["idUser"],
      "sLogin" => $_SESSION['modules']['login']["sLogin"],
      "sEmail" => $user->sEmail,
      "sProvider" => $_SESSION['modules']['login']["sProvider"]
    );
   $token = $tokenGenerator->generateToken($token_params);
   $db->exec('UPDATE `users` SET `sLastLoginDate`=NOW(), `sRecover` = NULL WHERE `id`='.$user->id);
   //echo json_encode(array("success" => true, "login" => $_GET["login"], 'token' => $token, 'provider' => 'password', 'hasPassword' => true, 'hasFacebook' => !!$user->facebook_id, 'hasGoogle' => ($user->google_id || $user->google_id_old)));
   echo json_encode(array("success" => true, "login" => $_GET["login"], 'token' => $token, 'provider' => 'password', 'hasPassword' => true, 'hasFacebook' => false, 'hasGoogle' =>false));
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

function updatePassword($db, $newPassword) {
   if (!isset($_SESSION) || !isset($_SESSION['modules']['login']) || !$newPassword) {
      echo json_encode(array('success' => false, 'error' => 'you must be logged in to change password', 'newPassword' => $newPassword));
      return;
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

function recoverPassword($db) {
   $recoverLogin = $_GET['recoverLogin'];
   $recoverEmail = $_GET['recoverEmail'];
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
      echo json_encode(array('success' => false, 'error' => 'Impossible de trouver un utilisateur correspondant dans la base.'));
      return;
   }
   if (!$user->sPasswordMd5) {
      echo json_encode(array('success'=>false, 'error' => 'L\'utilisateur demandé n\'avait pas de mot de passe et s\'authentifiait par une autre méthode.'));
      return;
   }
   if (!$user->sEmail) {
      echo json_encode(array('success'=>false, 'error' => 'L\'utilisateur demandé n\'a pas enregistré d\'adresse email.'));
      return;
   }
   // TODO: faire un check sur sLastLoginDate
   $sLogin = $user->sLogin;
   $recoverCode = md5(time().randomPassword());
   $query = 'update users set sRecover=:sRecover where id = :id;';
   $stmt = $db->prepare($query);
   $stmt->execute(array('sRecover' => $recoverCode, 'id' => $user->id));
   $mailBody = "Bonjour,\n\nVotre identifiant est $sLogin.\n\nCliquez sur le lien suivant pour obtenir un nouveau mot de passe :\n\n https://loginaws.algorea.org/login.html?sLogin=$sLogin&sRecover=$recoverCode \n\nLe webmaster France-IOI";
   $mailTitle = "Récupération de compte sur France-IOI";
   $mailError = customSendMail($user->sEmail, $mailTitle, $mailBody);
   if ($mailError) {
      echo json_encode(array('success' => false, 'error' => 'Problème lors de l\'envoi du mail: '.$mailError.'.'));
      return;
   }
   echo json_encode(array('success' => true));
}

function checkRecoverCode($db) {
   $recoverCode = $_GET['recoverCode'];
   $recoverLogin = $_GET['recoverLogin'];
   if (!$recoverCode || !$recoverLogin) {
      echo json_encode(array('success' => false, 'error' => 'Vous n\'avez pas spécifié de code ou de login'));
      return;
   }
   $query = 'select id from users where sRecover = :sRecover and sLogin = :sLogin;';
   $stmt = $db->prepare($query);
   $stmt->execute(array('sRecover'=>$recoverCode, 'sLogin'=>$recoverLogin));
   $user = $stmt->fetchObject();
   if (!$user) {
      echo json_encode(array('success' => false, 'error' => 'Le code est erroné ou trop vieux.'));
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
      updatePassword($db, $_GET['newPassword']);
   } else if ($_GET['action'] == 'removeGoogle') {
      removeGoogle($db);
   } else if ($_GET['action'] == 'removeFacebook') {
      removeFacebook($db);
   } else if ($_GET['action'] == 'recoverPassword') {
      recoverPassword($db);
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
      echo json_encode(array("success" => true, "login" => $loginParams['login'], 'token' => $loginParams['token'], 'provider' => 'facebook', 'loginParams'=>$loginParams));
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
      $id_token = json_decode($_SESSION['access_token'], true);
      // id_token here is a JWT signed by Google, containing some infos. As we have
      // set our scope to "openid" and also because we have set "openid.realm" in
      // the query url (see loginManager.js), this token will contain a "openid_id"
      // field containing the old id.
      $id_token = $id_token['id_token'];
      // decoding and verifying signature of JWT token
      $token_infos = $client->verifyIdToken($id_token);
      $token_infos = $token_infos->getAttributes();
      // now we can have our old id (that was not obvious!):
      $openid_id = $token_infos['payload']['openid_id'];
      // the new id is in the "sub" field
      $sub = $token_infos['payload']['sub'];
      if(!isset($_SESSION['modules']['login']['sEmail']) || !$_SESSION['modules']['login']['sEmail']) {
         $_SESSION['modules']['login']["sEmail"] = $token_infos['payload']['email'];
      }
      $_SESSION['modules']['login']["sProvider"] = "google";
      $loginParams = validateUserGoogle($db, $sub, $openid_id);
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
            ";
            if (isset($loginParams['sucess'])) {
               if ($loginParams['success']) {
                  echo 'opener.loginManager.googleAdded(true);';
               } else {
                  echo 'opener.loginManager.googleAdded(false, '.$loginParams['error'].');';
               }
            } else if ($validated) {
               echo "opener.loginManager.logged('".$loginParams['login']."', '".$loginParams['token']."');";
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