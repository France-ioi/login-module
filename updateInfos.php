<?php

require_once __DIR__.'/config.php';
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__."/lib/connect.php";
require_once __DIR__."/lib/session.php";
require_once __DIR__."/shared/TokenGenerator.php";

if (!isset($login_session['idUser']) || !$login_session['idUser']) {
	echo json_encode(["success" => false, 'error' => 'you cannot change user information when not logged']);
	exit();
}

if (!isset($_POST['infos']) || !count($_POST['infos'])) {
	echo json_encode(["success" => false, 'error' => 'missing info fields']);
	exit();
}

$db = connect();

$stmt = $db->prepare('update users set sFirstName = :sFirstName, sLastName = :sLastName, sStudentId = :sStudentId, sSex = :sSex, sEmail = :sEmail where id = :idUser;');
$stmt->execute([
	'sFirstName' => $_POST['infos']['sFirstName'],
	'sLastName' => $_POST['infos']['sLastName'],
	'sStudentId' => $_POST['infos']['sStudentId'],
	'sSex' => $_POST['infos']['sSex'],
    'sEmail' => $_POST['infos']['sEmail'],
	'idUser' => $login_session['idUser']
]);

$_SESSION['modules']['login']['sFirstName'] = $_POST['infos']['sFirstName'];
$_SESSION['modules']['login']['sLastName'] = $_POST['infos']['sLastName'];
$_SESSION['modules']['login']['sStudentId'] = $_POST['infos']['sStudentId'];
$_SESSION['modules']['login']['sSex'] = $_POST['infos']['sSex'];
$_SESSION['modules']['login']['sEmail'] = $_POST['infos']['sEmail'];

// then return session with new token:

include 'session.php';
