<?php

namespace App\LoginModule\DataSync;

use DB;
use App\LoginModule\DataSync\Data;
use App\User;
use App\Badge;
use App\Email;
use App\AuthConnection;


class Migrator {

    const CHUNK_SIZE = 100;

    public function migrate() {
        $offset = 0;
        while(count($users = Data::queryUsers($offset, self::CHUNK_SIZE))) {
            foreach($users as $user_data) {
                DB::transaction(function() use ($user_data) {
                    $user = $this->syncUser($user_data);
                    $this->syncEmail($user, $user_data);
                    $badges = Data::queryBadges($user_data['id']);
                    foreach($badges as $badge_data) {
                        $this->syncBadge($user, $badge_data);
                    }
                    //$auths = Data::queryAuths($user_data['id']);
                    //$this->syncAuthConnections($user, $user_data, $auths);
                });
            }
            $offset += self::CHUNK_SIZE;
        }
    }


    private function syncUser($user_data) {
        if($user = User::find($user_data['id'])) {
            $user->fill($user_data);
        } else {
            $user = new User($user_data);
            $user->id = $user_data['id'];
        }
        $user->admin = $user_data['admin'];
        $user->save();
        return $user;
    }


    private function syncEmail($user, $user_data) {
        if($email = $user->emails()->primary()->first()) {
            if($this->isEmailUsed($user_data['email'], $email->id)) {
                return false;
                //TODO: msg
            }
            $email->fill([
                'email' => $user_data['email'],
                'verified' => $user_data['email_verified']
            ]);
            $email->save();
        } else {
            if($this->isEmailUsed($user_data['email'])) {
                return false;
                //TODO: msg
            }
            $email = new Email([
                'email' => $user_data['email'],
                'role' => 'primary',
                'verified' => $user_data['email_verified']
            ]);
            $user->emails()->save($email);
        }
    }


    private function isEmailUsed($email, $except_id = null) {
        $q = Email::where('email', $email);
        if($except_id) {
            $q->where('id', '<>', $except_id);
        }
        return $q->first();
    }


    private function syncBadge($user, $badge_data) {
        if($badge = Badge::find($badge_data['id'])) {
            $badge->fill($badge_data);
            $badge->save();
        } else {
            $badge = new Badge($badge_data);
            $badge->id = $badge_data['id'];
            $user->badges()->save($badge);
        }
    }


    private function syncAuths($user, $user_data, $auths) {
        $connections = [];
        if($user_data['uid']) {
            $provider = false;
            if(strlen($user_data['uid']) == 16) {
                $connections['facebook'] = $user_data['uid'];
            } else {
                $connections['google'] = $user_data['uid'];
            }
        }
        foreach($auths as $auth) {
            if($auth['idAuth'] == 5) {
                $connections['pms'] = $auth['authStr'];
            } elseif (strpos($auth['authStr'], '::') !== false) {
                $connections['lti'] = $auth['authStr'];
            }
        }

        foreach($user->auth_connections as $auth_connection) {
            if(isset($connections[$auth_connection->provider])) {
                unset($connections[$auth_connection->provider]);
            }
        }

        foreach($connections as $provider => $uid) {
            $user->auth_connections()->save(new AuthConnection([
                'uid' => $uid,
                'provider' => $provider,
                'active' => false,
                'access_token' => ''
            ]));
        }

    }

}