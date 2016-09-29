<!doctype html>
<html>
<head></head>
<body>
<?php

require_once __DIR__.'/config.php';
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__."/lib/connect.php";
require_once __DIR__."/lib/session.php";
require_once __DIR__."/translate.inc.php";
require_once __DIR__."/shared/TokenGenerator.php";


if (!isset($_SESSION) || !isset($_SESSION['modules']) || !isset($_SESSION['modules']['login']) || !isset($_SESSION['modules']['login']['idUser'])) {
  echo 'You can only access this page when logged, please <a href="login.html">login</a> and come back.';
  exit();
}

$db = connect();

$redirectUrl = isset($_POST['redirectUrl']) ? $_POST['redirectUrl'] : null;
if (!$redirectUrl) {
	?>
	Vous pouvez ici être redirigé vers une url qui sera appelée avec un token de login. Pour une plateforme Algoréa, vous pouvez par exemple envoyer vers l'url suivante :
	<form method="post">
	<input type="text" name="redirectUrl" value="http://xxx.algorea.org/login/loginToken-entry.php" style="width:500px;">
	<input type="submit" value="aller">
	</form>
	<?php
} else {
	$tokenGenerator = new TokenGenerator($config->login_module->name, $config->login_module->private_key);

	$tokenParams = [
	  	"idUser" => $_SESSION['modules']['login']['idUser'],
	  	"sLogin" => $_SESSION['modules']['login']['sLogin']
		];
	$token = $tokenGenerator->generateToken($tokenParams);

	?>
	<script>
	var url = '<?= $redirectUrl.'?loginToken='.$token ?>';
	window.location.replace(url);
	</script>		
	<?php
}
?>
</body>
</html>