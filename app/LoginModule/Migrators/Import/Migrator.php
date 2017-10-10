<?php

namespace App\LoginModule\Migrators\Import;

use DB;
use App\User;
use App\Badge;
use App\Email;
use App\ObsoletePassword;
use App\AuthConnection;


class Migrator
{

    const CHUNK_SIZE = 100;

    protected $command;
    protected $connection;


    public function __construct($command, $connection) {
        $this->command = $command;
        $this->connection = $connection;
    }


    public function run() {
        $offset = 0;
        while(count($users = Data::queryUsers($this->connection, $offset, self::CHUNK_SIZE))) {
            foreach($users as $user_data) {
                DB::transaction(function() use ($user_data) {
                    if($user = $this->syncUser($user_data)) {
                        $this->syncPassword($user, $user_data);
                        $this->syncEmail($user, $user_data);
                        $badges = Data::queryBadges($this->connection, $user_data['id']);
                        foreach($badges as $badge_data) {
                            $this->syncBadge($user, $badge_data);
                        }
                        $auths = Data::queryAuths($this->connection, $user_data['id']);
                        $this->syncAuths($user, $user_data, $auths);
                    }
                });
            }
            $offset += self::CHUNK_SIZE;
        }
        $this->command->info('Completed.');
    }


    private function syncUser($user_data) {
        unset($user_data['password']);
        if($user = User::find($user_data['id'])) {
            if($this->isLoginUsed($user_data['login'], $user->id)) {
                return;
            }
            $user->fill($user_data);
        } else {
            if($this->isLoginUsed($user_data['login'])) {
                return;
            }
            $user = new User($user_data);
            $user->id = $user_data['id'];
        }
        //$user->admin = $user_data['admin'];
        $user->save();
        return $user;
    }


    private function syncPassword($user, $user_data) {
        if(!empty($user_data['password']) &&
            !$user->obsolete_passwords()->where('password', $user_data['password'])->first()) {
            $user->obsolete_passwords()->save(new ObsoletePassword($user_data));
        }
    }



    private function syncEmail($user, $user_data) {
        if($email = $user->emails()->primary()->first()) {
            if($this->isEmailUsed($user_data['email'], $email->id)) {
                return;
            }
            $email->fill([
                'email' => $user_data['email'],
                'verified' => true, //$user_data['email_verified']
            ]);
            $email->save();
        } else {
            if($this->isEmailUsed($user_data['email'])) {
                return;
            }
            $email = new Email([
                'email' => $user_data['email'],
                'role' => 'primary',
                'verified' => true, //$user_data['email_verified']
            ]);
            $user->emails()->save($email);
        }
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

            $facebook_prefix = 'http://www.facebook.com/';
            if(strpos($user_data['uid'], $facebook_prefix) !== false) {
                $connections['facebook'] = str_replace($facebook_prefix, '', $user_data['uid']);
            } else {
                $connections['google'] = $user_data['uid'];
            }
        }
        foreach($auths as $auth) {
            if($auth['idAuth'] == 5) {
                //$connections['pms'] = $auth['authStr'];
            } elseif (strpos($auth['authStr'], '::') !== false) {
                $connections['lti'] = $auth['authStr'];
            }
        }

        foreach($user->auth_connections as $exists_connection) {
            if(isset($connections[$exists_connection->provider])) {
                $exists_connection->uid = $connections[$exists_connection->provider];
                $exists_connection->save();
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


    private function isEmailUsed($email, $except_id = null) {
        $q = Email::where('email', $email);
        if($except_id) {
            $q->where('id', '<>', $except_id);
        }
        if($q->first()) {
            $this->command->error('Duplicate email: '.$email);
            return true;
        }
        return false;
    }


    private function isLoginUsed($login, $except_id = null) {
        if(is_null($login)) {
            return false;
        }
        $q = User::where('login', $login);
        if($except_id) {
            $q->where('id', '<>', $except_id);
        }
        if($q->first()) {
            $this->command->error('Duplicate login: '.$login);
            return true;
        }
        return false;
    }

}