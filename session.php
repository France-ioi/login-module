<?php

/* server-side session handling:
 * call it with ajax to get json-encoded parameters of the session as well
 * as a token
 */

// XXX Security issue, should not be needed:
// header("Access-Control-Allow-Origin: *");

require_once __DIR__.'/config.php';
require_once __DIR__.'/lib/connect.php';
require_once __DIR__.'/lib/session.php';

require_once(dirname(__FILE__)."/shared/TokenGenerator.php");

$tokenGenerator = new TokenGenerator($config->login_module->name, $config->login_module->private_key);

$sessionParams = array("sLanguage", "idUser", "sLogin", "sEmail", "sProvider", "hasFacebook", "hasPassword", "hasGoogle", "sFirstName", "sLastName", "sStudentId", "aBadges", "aNotBadges", "sSex");
$tokenParamNames = array("idUser", "sLogin", "sEmail", "sFirstName", "sLastName", "aBadges", "sStudentId", "sSex");
$tokenParams = [];
$jsSession = [];

if (isset($_SESSION['modules']) && isset($_SESSION['modules']['login'])) {
   foreach ($sessionParams as $param) {
      if (isset($_SESSION['modules']['login'][$param])) {
         $jsSession[$param] = $_SESSION['modules']['login'][$param];
      }
   }
   foreach ($tokenParamNames as $param) {
      if (isset($_SESSION['modules']['login'][$param])) {
         $tokenParams[$param] = $_SESSION['modules']['login'][$param];
      }
   }
}

if (!empty($jsSession)) {
   $token = $tokenGenerator->generateToken($tokenParams);
   $jsSession['sToken'] = $token;
}

echo json_encode($jsSession);
