<?php

require_once __DIR__.'/config.php';
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__."/lib/connect.php";
require_once __DIR__.'/shared/TokenParser.php';
require_once __DIR__.'/shared/TokenGenerator.php';
require_once __DIR__.'/lib/session.php';
require_once __DIR__.'/lib/loginString.php';


$authId = isset($_GET['authId']) ? $_GET['authId'] : null;
if (!$authId) {
    die('missing authId param');
}

$redirectUrl = isset($_GET['redirectUrl']) ? $_GET['redirectUrl'] : null;
// if (!$redirectUrl) {
//     die('missing redirectUrl param');
// }

$db = connect();

$stmt = $db->prepare('select * from auths where ID = :authId;');
$stmt->execute(['authId' => $authId]);
$providerInfos = $stmt->fetch();

if (!$providerInfos) {
    die('cannot find provider '.$authId);
}

$provider = new \League\OAuth2\Client\Provider\BasicAuthProvider([
    'clientId'                => $providerInfos['client_id'],    // The client ID assigned to you by the provider
    'clientSecret'            => $providerInfos['client_password'],   // The client password assigned to you by the provider
    'redirectUri'             => $config->selfBaseUrl.'oauth2-client.php?authId='.$authId.'&redirectUrl='.urlencode($redirectUrl),
    'urlAuthorize'            => $providerInfos['urlAuthorize'],
    'urlAccessToken'          => $providerInfos['urlAccessToken'],
    'urlResourceOwnerDetails' => $providerInfos['urlResourceOwnerDetails'],
    'scopes'                  => 'authenticate'
]);

function getUser($db, $loginData, $authId) {
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
    $stmt->execute(['idAuth' => $authId, 'authStr' => $authStr]);
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
        $idUser = $user['ID'];
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

function finishLogin($resourceOwner) {
    global $db, $config;
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

    ?>

    <!doctype html>
    <html>
       <head>
       <script>
        window.top.location.href = "<?= $redirectUrl ?>";
        //console.error("<?= $redirectUrl ?>");
       </script>
       </head>
       <body>
       </body>
    </html>

    <?php
}

// If we don't have an authorization code then get one
if (!isset($_GET['code'])) {

    // Fetch the authorization URL from the provider; this returns the
    // urlAuthorize option and generates and applies any necessary parameters
    // (e.g. state).
    $authorizationUrl = $provider->getAuthorizationUrl();

    // Get the state generated for you and store it to the session.
    $_SESSION['oauth2state'] = $provider->getState();
    error_log($_SESSION['oauth2state']);

    // Redirect the user to the authorization URL.
    header('Location: ' . $authorizationUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    var_dump($_SESSION);
    var_dump($_GET['state']);
    var_dump($_GET['state'] !== $_SESSION['oauth2state']);
    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    try {

        // Try to get an access token using the authorization code grant.
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        // We have an access token, which we may use in authenticated
        // requests against the service provider's API.
        echo $accessToken->getToken() . "\n";
        echo $accessToken->getRefreshToken() . "\n";
        echo $accessToken->getExpires() . "\n";
        echo ($accessToken->hasExpired() ? 'expired' : 'not expired') . "\n";

        // Using the access token, we may look up details about the
        // resource owner.
        $resourceOwner = $provider->getResourceOwner($accessToken);

        var_export($resourceOwner->toArray());
        //finishLogin($resourceOwner);

        // The provider provides a way to get an authenticated API request for
        // the service, using the access token; it returns an object conforming
        // to Psr\Http\Message\RequestInterface.
        // $request = $provider->getAuthenticatedRequest(
        //     'GET',
        //     'http://brentertainment.com/oauth2/lockdin/resource',
        //     $accessToken
        // );

    } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

        // Failed to get the access token or user details.
        exit($e->getMessage());

    }

}