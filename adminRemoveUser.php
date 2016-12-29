<?php
require_once __DIR__.'/config.php';
require_once __DIR__.'/lib/connect.php';
require_once __DIR__.'/lib/session.php';
require_once __DIR__.'/lib/badge.php';
require_once __DIR__.'/lib/account.php';

?><!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Admin France-ioi : suppression d'un utilisateur</title>
<style>
body {font-family: Arial,sans-serif;}
input {margin-bottom:10px;}
</style>
<body><?php

if (!isset($_SESSION) || !isset($_SESSION['modules']) || !isset($_SESSION['modules']['login']) || !isset($_SESSION['modules']['login']['idUser']) || $_SESSION['modules']['login']['bIsAdmin'] != 1) {
  echo "Désolé, seuls les admins peuvent voir cette page! Merci de vous logger avec un compte admin sur <a href=\"login.html\">cette page</a>.";
  exit();
}

$db = connect();

function removeFromLogin($sLogin) {
	global $db;
	$stmt = $db->prepare('select id from users where sLogin = :sLogin;');
   	$stmt->execute(['sLogin' => $sLogin]);
   	$idUser = $stmt->fetchColumn();
   	if (!$idUser) {
   	  return ['success' => false, 'error' => "impossible de trouver l'utilisateur ".$sLogin];
   	}
   	return removeAccount($idUser);
}

if (isset($_POST['login']) && $_POST['login']) {
   $infos = removeFromLogin($_POST['login']);
   if (!$infos['success']) {
   	  	echo "<p>Une erreur est survenue : ".$infos['error'].'</p>';
   } else {
   		echo "<p>L'utilisateur ".$_POST['login']." a été supprimé avec succès.</p>";
   }
}

?>
<p>Cette page réservée aux admins supprime un utilisateur :</p>
<form method="post">
		Login :<br>
      <input id="login" type="text" name="login" size="50"><br>
      <input style="margin-top:20px" type="submit" value="Supprimer l'utilisateur">
</form>
</body></html>