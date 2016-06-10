<?php

require_once __DIR__.'/config.php';
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__."/lib/connect.php";
require_once __DIR__.'/shared/TokenParser.php';
require_once __DIR__.'/shared/TokenGenerator.php';
require_once __DIR__.'/lib/session.php';
require_once __DIR__.'/lib/loginString.php';


$db = connect();

$redirectUrl = isset($_GET['redirectUrl']) ? $_GET['redirectUrl'] : null;
if (!$redirectUrl) {
	die('missing redirectUrl param');
}

$token = isset($_GET['sToken']) ? $_GET['sToken'] : null;
if (!$token) {
	die('missing sToken param');
}

$platformName = isset($_GET['sPlatform']) ? $_GET['sPlatform'] : null;
if (!$token) {
	die('missing sPlatform param');
}

$stmt = $db->prepare('SELECT ID, public_key, name from auths where name = :name;');
$stmt->execute(['name' => $platformName]);
$authData = $stmt->fetch();
if (!$authData) {
	die('cannot find platform named '.$platformName);
}

$tokenParser = new TokenParser($authData['public_key'], $platformName, 'public');

$tokenParams = $tokenParser->decodeJWS($token);

if (!isset($tokenParams['loginData'])) {
	die('cannot find loginData array in token');
}

function getAuthStr($loginData) {
	if (!isset($loginData['type']) || !$loginData['type']) {
		die('no type if login data');
	}
	if ($loginData['type'] == 'lti') {
		if (!isset($loginData['lti_consumer_key']) || !$loginData['lti_consumer_key'] || !isset($loginData['lti_user_id']) || !$loginData['lti_user_id']) {
			die('missing lti_consumer_key or lti_user_id in loginData');
		}
		return $loginData['lti_consumer_key'].'::'.$loginData['lti_user_id'];
	} else {
		die('unrecognized login type');	
	}
}

function getUser($db, $loginData, $authData) {
	if (!$loginData) {
		die('no loginData passed to getUser');
	}
	$eMail = $loginData['email'];
	$user = null;
	if (!$eMail) {
		$stmt = $db->prepare('select users.* where sEmail = :eMail');
		$stmt->execute(['eMail' => $eMail]);
		$user = $stmt->fetch();
	}
	$authStr = getAuthStr($loginData);
	$stmt = $db->prepare('select users_auths.* from users_auths where idAuth = :idAuth and authStr = :authStr;');
	$stmt->execute(['idAuth' => $authData['ID'], 'authStr' => $authStr]);
	$userAuth = $stmt->fetch();
	if (!$user && !$userAuth) {
		// TODO: what if !firstName or !lastName ?
		$login = genLogin($db, $loginData['firstName'], $loginData['lastName'], 'ups_');
		$stmt = $db->prepare("INSERT INTO `users` (`sLogin`, `sEmail`, `sSalt`, `sPasswordMd5`, `sFirstName`, `sLastName`, `sRegistrationDate`, `sLastLoginDate`) ".
         "VALUES (:sLogin, :sEmail, '', '', :firstName, :lastName, NOW(), NOW())");
		$stmt->execute([
			'firstName' => $loginData['firstName'],
			'lastName' => $loginData['lastName'],
			'sEmail' => $loginData['email'],
			'sLogin' => $login
		]);
		$idUser = $db->lastInsertId();
		$user = ['sLogin' => $login, 'id' => $idUser, 'bIsAdmin' => 0];
	} else if (!$user && $userAuth) {
		$idUser = $userAuth['idUser'];
		$stmt = $db->prepare('select users.* from users where ID = :idUser');
		$stmt->execute(['idUser' => $idUser]);
		$user = $stmt->fetch();
		if (!$user) {
			error_log('users_auths id '.$userAuth['ID'].' points to unexisitng user '.$userAuth['idUser']);
			die('user_auth does not correspond to any user');
		}
	} else { // user
		$idUser = $user['id'];
		if ($userAuth && ($user['id'] != $userAuth['idUser'])) {
			error_log('email '.$user['email'].' corresponds to user '.$user['id'].', but user_auth '.$userAuth['ID'].' points to user'.$userAuth['idUser']);
			die('email corresponds to an user, but user_auth points to a different user');
		}
	}
	if (!$userAuth) {
		$stmt = $db->prepare('INSERT INTO `users_auths` (`idUser`, `idAUth`, `authStr`) VALUES (:idUser, :idAuth, :authStr);');
		$stmt->execute([
			'idUser' => $idUser,
			'idAuth' => $authData['ID'],
			'authStr' => $authStr
		]);
	}
	return $user;
}

function getUserToken($db, $user, $tokenGenerator) {
	$tokenParams = [
      	"idUser" => $user['id'],
      	"sLogin" => $user['sLogin']
   	];
	$token = $tokenGenerator->generateToken($tokenParams);
	return $token;
}

$user = getUser($db, $tokenParams['loginData'], $authData);

if (isset($_SESSION['modules'])) {
  $_SESSION['modules']['login'] = array();
} else {
  $_SESSION['modules'] = array('login' => array());
}
$_SESSION['modules']['login']["idUser"] = $user['id'];
$_SESSION['modules']['login']["sLogin"] = $user['sLogin'];
$_SESSION['modules']['login']["bIsAdmin"] = $user['bIsAdmin'];
$_SESSION['modules']['login']["sProvider"] = "lti";
$_SESSION['modules']['login']["hasPassword"] = false;
$_SESSION['modules']['login']["hasGoogle"] = false;
$_SESSION['modules']['login']["hasFacebook"] = false;

$tokenGenerator = new TokenGenerator($config->login_module->name, $config->login_module->private_key);

$loginToken = getUserToken($db, $user, $tokenGenerator);

$redirectUrl = $redirectUrl . (strpos($redirectUrl, '?') === false ? '?' : '&') . 'loginToken=' . $loginToken;

$user['sPasswordMd5'] = null;
$user['sSalt'] = null;

?>

<!doctype html>
<html>
   <head>
   <script>
    var redirectUrl = <?= json_encode($redirectUrl); ?>;
    var user = <?= json_encode($user); ?>;
    var token = <?= json_encode($loginToken); ?>;
    if (redirectUrl) {
   		window.top.location.href = redirectUrl;
    } else if (window.opener) {
    	window.opener.loginManager.logged(user.sLogin, token);
    	window.close();
    } else {
    	console.error('I don\'t know what to do!');
    }
   	//console.error("<?= $redirectUrl ?>");
   </script>
   </head>
   <body>
   </body>
</html>