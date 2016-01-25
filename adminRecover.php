<?php

require_once __DIR__.'/config.php';
require_once __DIR__.'/lib/connect.php';
require_once __DIR__.'/lib/session.php';
require_once __DIR__.'/validateUser.php';

?><!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Admin France-ioi : récupération de mot de passe</title>
<style>
body {font-family: Arial,sans-serif;}
input {margin-bottom:10px;}
</style>
<body><?php

if (!isset($_SESSION) || !isset($_SESSION['modules']) || !isset($_SESSION['modules']['login']) || !isset($_SESSION['modules']['login']['idUser']) || $_SESSION['modules']['login']['bIsAdmin'] != 1) {
  echo "Désolé, seuls les admins peuvent voir cette page! Merci de vous logger avec un compte admin sur <a href=\"login.html\">cette page</a>.";
  exit();
}

$recoverLink = null;
if ($_POST['login'] || $_POST['email']) {
   $recoverLink = getRecoverLink($db, $_POST['email'], $_POST['login'], false);
}

if ($recoverLink) {
	echo "<p>Le lien de réinitialisation est <a href=\"".$recoverLink."\">".$recoverLink."</a></p>";
}

?>
<p>Cette page réservée aux admins donne un lien de réinitialisation de mot de passe à partir d'un login ou d'un email :</p>
<form method="post">
		Login :<br>
      <input id="login" type="text" name="login" size="50"><br>
      ou Email :<br>
      <input id="email" type="text" name="email" size="50"><br>
      <input style="margin-top:20px" type="submit" value="Générer un lien de réinitialisation">
</form>
</body></html>