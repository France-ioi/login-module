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

function getAuthStr($loginData) {
    if (isset($loginData['nickName']) && $loginData['nickName']) {
        return $loginData['nickName'];
    } elseif (isset($loginData['eMail']) && $loginData['eMail']) {
        return $loginData['eMail'];
    } else {
        die('cannot find a proper way of identifiying user in the login data '.json_encode($loginData));
    }
}

function loginTaken($login) {
    global $db;
    $stmt = $db->prepare('select ID from users where sLogin = :login;');
    $stmt->execute(['login' => $login]);
    return !!$stmt->fetch();
}

function getUser($db, $loginData, $providerInfos) {
    global $authId;
    if (!$loginData) {
        die('no loginData passed to getUser');
    }
    $eMail = isset($loginData['eMail']) ? $loginData['eMail'] : '';
    $user = null;
    if ($eMail) {
        $stmt = $db->prepare('select * from users where sEmail = :eMail');
        $stmt->execute(['eMail' => $eMail]);
        $user = $stmt->fetch();
    }
    $authStr = getAuthStr($loginData);
    $stmt = $db->prepare('select users_auths.* from users_auths where idAuth = :idAuth and authStr = :authStr;');
    $stmt->execute(['idAuth' => $authId, 'authStr' => $authStr]);
    $userAuth = $stmt->fetch();
    $sSex = null;
    if (isset($loginData['gender'])) {
        $sSex = $loginData['gender'] == 'm' ? 'Male' : 'Female';
    }
    $sAddress = '';
    if (isset($loginData['street1'])) {
        $sAddress = $loginData['street1'];
    }
    if (isset($loginData['street2'])) {
        $sAddress .= $loginData['street2'];
    }
    if (!$user && !$userAuth) {
        $login = null;
        if (!isset($loginData['nickName'])) {
            $login = genLogin($db, $loginData['firstName'], $loginData['lastName'], 'pms_');
        } else {
            $login = $loginData['nickName'];
            if (loginTaken($login)) {
                error_log('auth id '.$authId.' transmitted a login already taken: '.$login);
                if (loginTaken('pms_'.$login)) {
                    error_log('pms_'.$login.' taken too!');
                    // TODO: what if !firstName or !lastName ?
                    $login = genLogin($db, $loginData['firstName'], $loginData['lastName'], 'pms_');
                } else {
                    $login = 'pms_'.$login;
                }
            }
        }

        $stmt = $db->prepare("INSERT INTO `users` (`sLogin`, `sEmail`, `sSalt`, `sPasswordMd5`, `sFirstName`, `sLastName`, `sRegistrationDate`, `sLastLoginDate`, `sBirthDate`, `sSex`, `sZipCode`, `sCity`, `sAddress`) ".
         "VALUES (:sLogin, :sEmail, '', '', :firstName, :lastName, NOW(), NOW(), :sBirthDate, :sSex, :sZipCode, :sCity, :sAddress);");
        $stmt->execute([
            'firstName' => (isset($loginData['firstName']) ? $loginData['firstName'] : null),
            'lastName' => (isset($loginData['lastName']) ? $loginData['lastName'] : null),
            'sEmail' => $eMail,
            'sBirthDate' => (isset($loginData['dateOfBirth']) ? $loginData['dateOfBirth'] : null),
            'sLogin' => $login,
            'sSex' => $sSex,
            'sZipCode' => (isset($loginData['zip']) ? $loginData['zip'] : null),
            'sCity' => (isset($loginData['city']) ? $loginData['city'] : null),
            'sAddress' => $sAddress,
        ]);
        $idUser = $db->lastInsertId();
        $user = ['sLogin' => $login, 'id' => $idUser, 'bIsAdmin' => 0, 'sEmail' => $eMail, 'sFirstName' => $loginData['firstName'], 'sLastName' => $loginData['lastName']];
    } else if (!$user && $userAuth) {
        $idUser = $userAuth['idUser'];
        $stmt = $db->prepare('select * from users where ID = :idUser');
        $stmt->execute(['idUser' => $idUser]);
        $user = $stmt->fetch();
        if (!$user) {
            error_log('users_auths id '.$userAuth['ID'].' points to unexisitng user '.$userAuth['idUser']);
            die('user_auth does not correspond to any user');
        } else {
            // TODO: update only set fields?
            $stmt = $db->prepare("update `users` set `sEmail` = :sEmail, `sFirstName` = :sFirstName, `sLastName` = :sLastName, `sLastLoginDate` = NOW(), `sBirthDate` = :sBirthDate, `sSex` = :sSex, `sZipCode` = :sZipCode, `sCity` = :sCity, `sAddress` = :sAddress where id = :idUser;");
            $stmt->execute([
                'idUser' => $idUser,
                'sFirstName' => (isset($loginData['firstName']) ? $loginData['firstName'] : null),
                'sLastName' => (isset($loginData['lastName']) ? $loginData['lastName'] : null),
                'sEmail' => $eMail,
                'sBirthDate' => (isset($loginData['dateOfBirth']) ? $loginData['dateOfBirth'] : null),
                'sSex' => $sSex,
                'sZipCode' => (isset($loginData['zip']) ? $loginData['zip'] : null),
                'sCity' => (isset($loginData['city']) ? $loginData['city'] : null),
                'sAddress' => $sAddress,
            ]);
        }
    } else { // user
        $idUser = $user['id'];
        if ($userAuth && ($user['id'] != $userAuth['idUser'])) {
            error_log('email '.$user['email'].' corresponds to user '.$user['id'].', but user_auth '.$userAuth['ID'].' points to user'.$userAuth['idUser']);
            die('email corresponds to an user, but user_auth points to a different user');
        }
        // TODO: update only set fields?
        $stmt = $db->prepare("update `users` set `sEmail` = :sEmail, `sFirstName` = :sFirstName, `sLastName` = :sLastName, `sLastLoginDate` = NOW(), `sBirthDate` = :sBirthDate, `sSex` = :sSex, `sZipCode` = :sZipCode, `sCity` = :sCity, `sAddress` = :sAddress where id = :idUser;");
        $stmt->execute([
            'idUser' => $idUser,
            'sFirstName' => (isset($loginData['firstName']) ? $loginData['firstName'] : null),
            'sLastName' => (isset($loginData['lastName']) ? $loginData['lastName'] : null),
            'sEmail' => $eMail,
            'sBirthDate' => (isset($loginData['dateOfBirth']) ? $loginData['dateOfBirth'] : null),
            'sSex' => $sSex,
            'sZipCode' => (isset($loginData['zip']) ? $loginData['zip'] : null),
            'sCity' => (isset($loginData['city']) ? $loginData['city'] : null),
            'sAddress' => $sAddress,
        ]);
    }
    if (!$userAuth) {
        $userAuth = [
            'idUser' => $idUser,
            'idAuth' => $authId,
            'authStr' => $authStr
        ];
        $stmt = $db->prepare('INSERT INTO `users_auths` (`idUser`, `idAUth`, `authStr`) VALUES (:idUser, :idAuth, :authStr);');
        $stmt->execute($userAuth);
        $userAuth['ID'] = $db->lastInsertId();
    }
    fillBadges($user, $loginData, $providerInfos);
    return $user;
}

function fillBadges($user, $loginData, $providerInfos) {
    // this function hardcodes many things, I'm not sure how it could be done otherwise
    $sBadge = null;
    if ($providerInfos['name'] == 'PMS') {
        if (isset($loginData['schoolId']) && $loginData['schoolId'] && isset($loginData['schoolClass']) && $loginData['schoolClass']) {
            $sBadge = 'groups://PMS/'.$loginData['schoolId'].'/'.$loginData['schoolClass'].'/member';
        }
    }
    if ($sBadge) {
        $stmt = $db->prepare('SELECT id from `user_badges` where `idUser` = :idUser and sBadge = :sBadge;');
        $stmt->execute(['idUser' => $user['id'], 'sBadge' => $sBadge]);
        $idBadge = $stmt->fetchColumn();
        if (!$idBadge) {
            $stmt = $db->prepare('INSERT INTO `user_badges` (`idUser`, `sBadge`) VALUES (:idUser, :sBadge);');
            $stmt->execute(['idUser' => $user['id'], 'sBadge' => $sBadge]);
        }
    }
}

function getBadges($db, $user) {
    $stmt = $db->prepare('select sBadge from user_badges where idUser = :idUser;');
    $stmt->execute(['idUser' => $user['id']]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getUserToken($db, $user, $tokenGenerator, $badges) {
    $tokenParams = [
        "idUser" => $user['id'],
        "sLogin" => $user['sLogin'],
        "sEmail" => $user['sEmail'],
        "sFirstName" => $user['sFirstName'],
        "sLastName" => $user['sLastName'],
        "sLastName" => $user['sStudentId'],
        "aBadges" => $badges
    ];
    $token = $tokenGenerator->generateToken($tokenParams);
    return $token;
}

function finishLogin($resourceOwner) {
    global $db, $config, $providerInfos, $redirectUrl;
    $user = getUser($db, $resourceOwner, $providerInfos);
    $badges = getBadges($db, $user);

    if (isset($_SESSION['modules'])) {
      $_SESSION['modules']['login'] = array();
    } else {
      $_SESSION['modules'] = array('login' => array());
    }
    $_SESSION['modules']['login']["idUser"] = $user['id'];
    $_SESSION['modules']['login']["sLogin"] = $user['sLogin'];
    $_SESSION['modules']['login']["sEmail"] = $user['sEmail'];
    $_SESSION['modules']['login']["sFirstName"] = $user['sFirstName'];
    $_SESSION['modules']['login']["sLastName"] = $user['sLastName'];
    $_SESSION['modules']['login']["sStudentId"] = $user['sStudentId'];
    $_SESSION['modules']['login']["aBadges"] = $badges;
    $_SESSION['modules']['login']["bIsAdmin"] = $user['bIsAdmin'];
    $_SESSION['modules']['login']["sProvider"] = "oauth";
    $_SESSION['modules']['login']["hasPassword"] = false;
    $_SESSION['modules']['login']["hasGoogle"] = false;
    $_SESSION['modules']['login']["hasFacebook"] = false;

    $tokenGenerator = new TokenGenerator($config->login_module->name, $config->login_module->private_key);

    $loginToken = getUserToken($db, $user, $tokenGenerator, $badges);
    
    if ($redirectUrl) {
        $redirectUrl = $redirectUrl . (strpos($redirectUrl, '?') === false ? '?' : '&') . 'loginToken=' . $loginToken;
    }

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
    var loginData = <?= json_encode($_SESSION['modules']['login'], JSON_UNESCAPED_UNICODE); ?>;
    if (redirectUrl) {
        window.top.location.href = redirectUrl;
    } else if (window.opener) {
        window.opener.loginManager.logged(user.sLogin, token, 'pms', loginData);
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

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    try {

        // Try to get an access token using the authorization code grant.
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        // Using the access token, we may look up details about the
        // resource owner.
        $resourceOwner = $provider->getResourceOwner($accessToken);

        finishLogin($resourceOwner->toArray());

    } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

        // Failed to get the access token or user details.
        exit($e->getMessage());

    }

}
