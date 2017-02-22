<?php

namespace App\LoginModule;

use App\Badge;

class Badges {

    static function post($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $res = curl_exec($ch);
        curl_close($ch);        
        $res = json_decode($res, true);
        return json_last_error() === JSON_ERROR_NONE ? $res : false;
    }


    static function verifyCode($badgeUrl, $code) {
        $post_request = ['action' => 'verifyCode', 'code' => $code];
        if($server_output = self::post($badgeUrl, $post_request)) {
            return ['success' => true, 'userInfos' => $server_output];    
        } else {
            return ['success' => false, 'error' => 'error_badge_code_invalid'];
        }
    }


    function removeByCode($badgeUrl, $code) {
        $post_request = ['action' => 'removeByCode', 'code' => $code];
        if($server_output = self::post($badgeUrl, $post_request)) {
            return ['success' => true];    
        } else {
            return ['success' => false, 'error' => 'error_badge_code_invalid'];
        }        
    }


    function removeUserBadges($user) {
        $badges = $user->badges()->where('do_not_possess', 0)->get();
        foreach ($badges as $badge) {
            // TODO: what if the jBadgeInfos is not a code?
            $jBadgeInfos = $badge['infos'];
            if($jBadgeInfos) {
                try {
                    $decBadgeInfos = json_decode($jBadgeInfos, true);
                } catch(Exception $e) {
                    return array('success' => false, 'error' => 'cannot read badge json infos: '.$e->getMessage());
                }
                if (!$decBadgeInfos['code']) {
                    return array('success' => false, 'error' => 'cannot read badge code from json infos');
                }
                $badgeRemovalInfos = self::removeByCode($badge['url'], $decBadgeInfos['code']);
                if (!$badgeRemovalInfos['success']) {
                    return $badgeRemovalInfos;
                }
            }
        }
    }

    function verifyBadge($badgeUrl, $verifInfos, $verifType) {
        if ($verifType == 'code') {
            if (!$verifInfos || !isset($verifInfos['code'])) {
                return ['success' => false, 'error' => 'missing code'];
            }
            return self::verifyCode($badgeUrl, $verifInfos['code']);
        } else {
            return ['success' => false, 'error' => 'unknown verif type '.$verifType];	
        }
    }

    function isBadgeRegistered($badgeUrl, $verifInfos, $verifType) {
        global $db;
        if ($verifType == 'code') {
            if (!$verifInfos || !isset($verifInfos['code'])) {
                return ['success' => false, 'error' => 'missing code'];
            }
            // WARNING: the serialization of $verifInfos into $jBadgeInfos *must* be unique
            //          in order to be able to perform textual comparisons in SQL
            $code = trim($verifInfos['code']);
            $jBadgeInfos = json_encode(['code' => $code], JSON_UNESCAPED_UNICODE);
            $res = $stmt->fetch();
            $res = Badge::where('infos', $jBadgeInfos)
                ->where('url', $badgeUrl)
                ->with('user')
                ->find();
            if (!$res) {
                return ['success' => true, 'result' => false];
            }
            return ['success' => true, 'result' => $res];
        } else {
            return ['success' => false, 'error' => 'unknown verif type '.$verifType];
        }
    }

    function addBadge($idUser, $badge, $badgeInfos, $verifType) {
        global $db;
        if (!$idUser || !$badge) return;
        $jBadgeInfos = $badgeInfos ? json_encode(['code' => trim($badgeInfos['code'])], JSON_UNESCAPED_UNICODE) : null;

        $res = Badge::where('user_id', $idUser)->where('url', $badge)->find();
        if(!$res) {
            Badge::create([
                'user_id' => $idUser,
                'url' => $badge,
                'infos' => $jBadgeInfos
            ]);
        }
    }

    function updateBadgeInfos($idUser, $badgeUrl, $badgeInfos, $verifType) {
        if (!$idUser || !$badgeUrl || !$badgeInfos || !$verifType || !isset($badgeInfos['code'])) return ['success' => false, 'error' => 'missing argument'];

        $post_data = ['action' => 'updateInfos'];
        if ($verifType == 'code') {
            $post_data['idUser'] = $idUser;
            $post_data['code'] = $badgeInfos['code'];
        } else {
            return ['success' => false, 'error' => 'unknown verification type: '.$verifType];
        }

        $server_output = self::post($badgeUrl, $post_data);

        if (!$server_output) {
            return ['success' => false, 'error' => 'badge/updateInfos failed! this should not happen!'];	
        }
        if (!$server_output['success']) {
            return ['success' => false, 'error' => 'badge/updateInfos failed: '.$server_output['error']];	
        }
        return $server_output;
    }

    function addBadgesInSession() {
        /*
        global $db;
        if (!isset($_SESSION['modules']['login']['idUser'])) {
            return;
        }
        $stmt = $db->prepare('select user_badges.sBadge, user_badges.bDoNotPossess from user_badges where idUser = :idUser;');
        $stmt->execute(['idUser' => $_SESSION['modules']['login']['idUser']]);
        $allBadges = $stmt->fetchAll();
        $aBadges = [];
        $notBadges = [];
        foreach ($allBadges as $badge) {
            if (!intval($badge['bDoNotPossess'])) {
                $aBadges[] = $badge['sBadge'];
            } else {
                $notBadges[] = $badge['sBadge'];
            }
        }
        $_SESSION['modules']['login']['aBadges'] = $aBadges;
        $_SESSION['modules']['login']['aNotBadges'] = $notBadges;
        */
    }

    function verifyAndAddBadge($idUser, $badgeUrl, $verifInfos, $verifType) {
        $verifData = self::verifyBadge($badgeUrl, $verifInfos, $verifType);
        if (!$verifData['success']) {
            return $verifData;
        }
        self::addBadge($idUser, $badgeUrl, $verifInfos, $verifType);
        return self::updateBadgeInfos($idUser, $badgeUrl, $verifInfos, $verifType);
    }

    function attachBadge($idUser, $badgeUrl, $verifInfos, $verifType) {
        if ($verifType != 'code') {
            return ['success' => false, 'error' => '0:unknown verification type: '.$verifType];
        }
        if (!isset($verifInfos['code']) || !$verifInfos['code']) {
            return ['success' => false, 'error' => 'missing badge code'];
        }
        $badgeRegistered = self::isBadgeRegistered($badgeUrl, $verifInfos, $verifType);
        if (!$badgeRegistered['success']) {
            return $badgeRegistered;
        }
        if ($badgeRegistered['result'] != false) {
            if ($badgeRegistered['result']['idUser'] == $idUser) {
                return ['success' => false, 'error' => 'error_code_registered_already'];
            } else {
                return ['success' => false, 'error' => 'error_code_used', 'errorArgs' => ['login' => $badgeRegistered['result']['sLogin']]];
            }
        }
        return self::verifyAndAddBadge($idUser, $badgeUrl, $verifInfos, $verifType);
    }

}