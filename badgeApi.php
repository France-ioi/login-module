<?php

require_once __DIR__.'/lib/connect.php';
require_once __DIR__.'/lib/session.php';
require_once __DIR__.'/lib/badge.php';
require_once __DIR__.'/lib/account.php';
require_once __DIR__.'/lib/loginString.php';

function getInfos($badgeUrl, $verifInfos, $verifType) {
	global $db;
	$badgeRegistered = isBadgeRegistered($badgeUrl, $verifInfos, $verifType);
	if (!$badgeRegistered['success']) {
		echo json_encode($badgeRegistered);
		exit();
	}
	if ($badgeRegistered['result'] != false) {
		echo json_encode(['success' => false, 'error' => 'error_code_used', 'errorArgs' => ['login' => $badgeRegistered['result']['sLogin']]]);
		exit();	
	}
	$res = verifyBadge($badgeUrl, $verifInfos, $verifType);
	if ($res['success']) {
		$res['userInfos']['sLogin'] = genLogin($db, $res['userInfos']['sFirstName'], $res['userInfos']['sLastName'], '');
	}
	echo json_encode($res);
}

function attachBadge($badgeUrl, $verifInfos, $verifType) {
	global $db;
	if (!isset($_SESSION['modules']['login']['idUser'])) {
		echo json_encode(['success' => false, 'error' => 'you must be logged in to attach a badge to your account!']);
		exit();
	}
	if ($verifType != 'code') {
		echo json_encode(['success' => false, 'error' => '0:unknown verification type: '.$verifType]);
		exit();
	}
	if (!isset($verifInfos['code']) || !$verifInfos['code']) {
		echo json_encode(['success' => false, 'error' => 'missing badge code']);
		exit();
	}
	$badgeRegistered = isBadgeRegistered($badgeUrl, $verifInfos, $verifType);
	if (!$badgeRegistered['success']) {
		echo json_encode($badgeRegistered);
		exit();
	}
	if ($badgeRegistered['result'] != false) {
		if ($badgeRegistered['result']['idUser'] == $_SESSION['modules']['login']['idUser']) {
			echo json_encode(['success' => false, 'error' => 'error_code_registered_already']);
			exit();	
		} else {
			echo json_encode(['success' => false, 'error' => 'error_code_used', 'errorArgs' => ['login' => $badgeRegistered['result']['sLogin']]]);
			exit();	
		}
	}
	$infos = verifyAndAddBadge($badgeUrl, $verifInfos, $verifType);
	echo json_encode($infos);
}

function iDontHaveThisBadge($badgeUrl) {
	global $db;
	if (!isset($_SESSION['modules']['login']['idUser'])) {
		echo json_encode(['success' => false, 'error' => 'you must be logged in to attach a badge to your account!']);
		exit();
	}

	$stmt = $db->prepare('select * from user_badges where idUser = :idUser and sBadge = :sBadge;');
	$stmt->execute(['idUser' => $_SESSION['modules']['login']['idUser'], 'sBadge' => $badgeUrl]);
	$res = $stmt->fetch();
	if ($res) {
		if (!intval($res['bDoNotPossess'])) {
			echo json_encode(['success' => false, 'error' => 'you already have this badge!']);	
		} else {
			echo json_encode(['success' => false, 'error' => 'you already said you did not have this badge']);
		}
		exit();
	}
	$stmt = $db->prepare('insert into user_badges (idUser, sBadge, bDoNotPossess) values (:idUser, :sBadge, 1);');
	$stmt->execute(['idUser' => $_SESSION['modules']['login']['idUser'], 'sBadge' => $badgeUrl]);
	$_SESSION['modules']['login']['aNotBadges'][] = $badgeUrl;
	echo json_encode(['success' => true]);
}

function confirmAccountCreation($badgeUrl, $verifInfos, $verifType, $userInfos) {
	global $db;
	if (isset($_SESSION['modules']['login']['idUser'])) {
		echo json_encode(['success' => false, 'error' => 'you are already logged in']);
		exit();
	}
	if ($verifType != 'code') {
		echo json_encode(['success' => false, 'error' => '1:unknown verification type: '.$verifType]);
		exit();
	}
	if (!isset($verifInfos['code']) || !$verifInfos['code']) {
		echo json_encode(['success' => false, 'error' => 'missing badge code']);
		exit();
	}
	$badgeRegistered = isBadgeRegistered($badgeUrl, $verifInfos, $verifType);
	if (!$badgeRegistered['success']) {
		echo json_encode($badgeRegistered);
		exit();
	}
	if ($badgeRegistered['result'] != false) {
		echo json_encode(['success' => false, 'error' => 'error_code_used', 'errorArgs' => ['login' => $badgeRegistered['result']['sLogin']]]);
		exit();	
	}
	$infos = verifyBadge($badgeUrl, $verifInfos, $verifType);
	if (!$infos['success']) {
		echo json_encode($infos);
		exit();
	}
	if (!isset($userInfos['sLogin']) || !$userInfos['sLogin'] || !isset($userInfos['sPassword']) || !$userInfos['sPassword']) {
		echo json_encode(['success' => false, 'error' => 'missing login or password']);
		exit();	
	}
	$userInfos['sLogin'] = trim($userInfos['sLogin']);
	$userInfos['sPassword'] = trim($userInfos['sPassword']);
	if (strlen($userInfos['sPassword']) < 6) {
		echo json_encode(["success" => false, "error" => 'error_password_length', 'errorArgs' => ['passwordLength' => '6']]);
		exit();
	}
	if (!isValidUsername($userInfos['sLogin'])) {
		echo json_encode(['success' => false, 'error' => 'error_allowed_symbols']);
		exit();	
	}
	if (isExistingUser($db, "sLogin", $userInfos['sLogin'])) {
      	echo json_encode(["success" => false, "error" => 'error_login_used']);
     	exit();
   	}
   	if (isset($userInfos['sEmail'])) {
   		$userInfos['sEmail'] = trim($userInfos['sEmail']);
   		if ($userInfos['sEmail']) {
			if (isExistingUser($db, "sEmail", $userInfos['sEmail'])) {
		      	echo json_encode(["success" => false, "error" => 'error_email_used']);
		     	exit();
		   	}
   		}
   	} else {
   		$userInfos['sEmail'] = null;
   	}
   	$idUser = createAccount($db, $userInfos['sLogin'], $userInfos['sEmail'], null, $userInfos['sPassword'], 'password');
   	$stmt = $db->prepare('update users set sFirstName = :sFirstName, sLastName = :sLastName, sSex = :sSex where id = :idUser;');
	$stmt->execute([
		'sFirstName' => $userInfos['sFirstName'],
		'sLastName' => $userInfos['sLastName'],
		'sSex' => $userInfos['sSex'],
		'idUser' => $idUser
	]);
	$_SESSION['modules']['login']["idUser"] = $idUser;
   	$_SESSION['modules']['login']["sLogin"] = $userInfos['sLogin'];
   	$_SESSION['modules']['login']["sProvider"] = 'password';
   	$_SESSION['modules']['login']["hasGoogle"] = false;
   	$_SESSION['modules']['login']["hasFacebook"] = false;
   	$_SESSION['modules']['login']["sFirstName"] = $userInfos['sFirstName'];
   	$_SESSION['modules']['login']["sLastName"] = $userInfos['sLastName'];
   	$_SESSION['modules']['login']["sEmail"] = $userInfos['sEmail'];
   	$_SESSION['modules']['login']["aBadges"] = [$badgeUrl];
   	addBadge($idUser, $badgeUrl, $verifInfos, $verifType);
   	$updateOk = updateBadgeInfos($idUser, $badgeUrl, $verifInfos, $verifType);
   	echo json_encode($updateOk);
}

if (!isset($_POST['action'])) {
	echo json_encode(['success' => false, 'error' => 'missing action']);
	exit();
}

$db = connect();

if ($_POST['action'] == 'getInfos') {
	if (!isset($_POST['badgeUrl']) || !$_POST['badgeUrl'] || !isset($_POST['verifInfos']) || !$_POST['verifInfos'] || !isset($_POST['verifType']) || !$_POST['verifType']) {
		echo json_encode(['success' => false, 'error' => 'missing argument']);
		exit();
	}
	getInfos($_POST['badgeUrl'], $_POST['verifInfos'], $_POST['verifType']);
} elseif ($_POST['action'] == 'confirmAccountCreation') {
	if (!isset($_POST['badgeUrl']) || !$_POST['badgeUrl'] || !isset($_POST['verifInfos']) || !$_POST['verifInfos'] || !isset($_POST['verifType']) || !$_POST['verifType'] || !isset($_POST['userInfos']) || !$_POST['userInfos']) {
		echo json_encode(['success' => false, 'error' => 'missing argument']);
		exit();
	}
	confirmAccountCreation($_POST['badgeUrl'], $_POST['verifInfos'], $_POST['verifType'], $_POST['userInfos']);
} elseif ($_POST['action'] == 'attachBadge') {
	if (!isset($_POST['badgeUrl']) || !$_POST['badgeUrl'] || !isset($_POST['verifInfos']) || !$_POST['verifInfos'] || !isset($_POST['verifType']) || !$_POST['verifType']) {
		echo json_encode(['success' => false, 'error' => 'missing argument']);
		exit();
	}
	attachBadge($_POST['badgeUrl'], $_POST['verifInfos'], $_POST['verifType']);
} elseif ($_POST['action'] == 'iDontHaveThisBadge') {
	if (!isset($_POST['badgeUrl']) || !$_POST['badgeUrl']) {
		echo json_encode(['success' => false, 'error' => 'missing argument']);
		exit();
	}
	iDontHaveThisBadge($_POST['badgeUrl'], $_POST['verifInfos'], $_POST['verifType']);
} else {
	echo json_encode(['success' => false, 'error' => 'unknown action action']);
}
