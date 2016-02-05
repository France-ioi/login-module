<?php

require_once __DIR__.'/../simplesamlphp/lib/_autoload.php';
require_once __DIR__.'/../config.php';
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__."/../lib/session.php";
require_once __DIR__."/../lib/connect.php";
require_once __DIR__."/../translate.inc.php";
require_once __DIR__."/../shared/TokenGenerator.php";

$thisUrl = $config->selfBaseUrl.'/saml.php';

$as = new SimpleSAML_Auth_Simple('default-sp');

$attributes = $as->getAttributes();

if (!count($attributes)) {
	$loggedIn = false;
} else {
	$loggedIn = true;
}

$loginUrl = 
$logoutUrl = $as->getLogoutURL();

function redirectTo($url) {
	header('Location: ' . $url);
	exit();
}

// returns 1 if user 
function logUser($attributes) {
	session_start();
	require_once 'connect.php';
	require_once 'connect.php';
   $query = "SELECT `id`, sLogin, sEmail FROM `users` WHERE `sOpenIdIdentity` = :samlID";
   $stmt = $db->prepare($query);
   $stmt->execute(['samlID' => 'toto']);
   if ($user = $stmt->fetch) {
   	if ($user->sLogin !== '') {
		   if (isset($_SESSION['modules'])) {
		      $_SESSION['modules']['login'] = array();
		   } else {
		      $_SESSION['modules'] = array('login' => array());
		   }
		   $_SESSION['modules']['login']["idUser"] = $user->id;
		   $_SESSION['modules']['login']["sLogin"] = $user->sLogin;
		   $_SESSION['modules']['login']["sProvider"] = "saml";
		   $_SESSION['modules']['login']["hasPassword"] = false;
		   $_SESSION['modules']['login']["hasGoogle"] = false;
		   $_SESSION['modules']['login']["hasFacebook"] = false;
		   $_SESSION['modules']['login']["hasSaml"] = false;
		   $token_params = array(
		      //"sLanguage" => $user->sDefaultLanguage,
		      "idUser" => $_SESSION['modules']['login']["idUser"],
		      "sLogin" => $_SESSION['modules']['login']["sLogin"],
		      "sEmail" => $user->sEmail,
		      "sProvider" => $_SESSION['modules']['login']["sProvider"]
		    );
		   $token = $tokenGenerator->generateToken($token_params);
		   $db->exec('UPDATE `users` SET `sLastLoginDate`=NOW(), `sRecover` = NULL WHERE `id`='.$user->id);
		   return $token;
   	} else {
   		return 0;
   	}
   } else {
   	return 0;
   }
}

if ($_GET['login']) {
	$url = $as->getLoginURL($thisUrl.'?fromLogin=1');
	print($url);
	//redirectTo($as->getLoginURL($thisUrl.'?fromLogin=1'));
}
elseif ($_GET['fromLogin']) {
	if (count($attributes)) {
		$token = listlogUser();
		if ($token == 0) {
			redirectTo($config->selfBaseUrl.'/login.html?newUserFromSaml=1');
		} else {
			$login = $_SESSION['modules']['login']["sLogin"];
			echo "<script>window.opener.logged(".$login.", ".$token.",'saml');window.close();</script>";	
		}
	} else {
		echo "<script>window.close();</script>";
	}
}
elseif ($_GET['logout']) {
	redirectTo($as->getLoginURL($thisUrl.'?fromLogout=1'));	
}
elseif ($_GET['fromLogout']) {
	echo "<script>window.close();</script>";
} else {
	echo "no action passed";
}