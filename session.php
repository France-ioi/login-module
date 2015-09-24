<?php

/* server-side session handling:
 * call it with ajax to get json-encoded parameters of the session as well
 * as a token
 */

header("Access-Control-Allow-Origin: *");

require_once __DIR__.'/connect.php';

require_once(dirname(__FILE__)."/shared/TokenGenerator.php");

$tokenGenerator = new TokenGenerator($config->login_module->name, $config->login_module->private_key);

session_start();

$params = array("sLanguage", "idUser", "sLogin", "sEmail", "sProvider", "hasFacebook", "hasPassword", "hasGoogle");
$jsSession = array();

if (isset($_SESSION['modules']) && isset($_SESSION['modules']['login'])) {
   foreach ($params as $param) {
      if (isset($_SESSION['modules']['login'][$param])) {
         $jsSession[$param] = $_SESSION['modules']['login'][$param];
      }
   }
}

if (!empty($jsSession)) {
   $token = $tokenGenerator->generateToken($jsSession);
   $jsSession['sToken'] = $token;
}

echo json_encode($jsSession);
