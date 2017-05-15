<?php

namespace App\LoginModule\AuthProviders;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use App\LoginModule\UserPassword;
use Illuminate\Support\Facades\DB;
use App\LoginModule\AuthConnector;
use App\Email;
use App\User;
use App\AuthConnection;
use App\ObsoletePassword;

class UserProvider extends EloquentUserProvider
{


    public function retrieveByCredentials(array $credentials) {
        if(empty($credentials)) {
            return;
        }

        // for pwd restore, it available for users with emails only
        if(isset($credentials['email'])) {
            $credentials['login'] = $credentials['email'];
            unset($credentials['email']);
        }

        $query = $this->createModel()->newQuery();

        if(strpos($credentials['login'], '@') === false) {
            $query->where('login', $credentials['login']);
        } else if($email = \App\Email::where('email', $credentials['login'])->first()) {
            $query->where('id', $email->user_id);
        } else return;

        $results = $query->first();
        if($results) {
            return $results;
        }

        // TODO :: Dirty fix to remove once JwInf 2017 is done
        // No results check bwinf_data
        if(strpos($credentials['login'], '@') === false) {
            $bwinf_user = DB::select('SELECT * FROM bwinf_data WHERE nickName = ?', [$credentials['login']]);
        } else {
            $bwinf_user = DB::select('SELECT * FROM bwinf_data WHERE eMail = ?', [$credentials['login']]);
        }
        // We have an user in bwinf_data, create it
        if(count($bwinf_user) == 1) {
            // Yes, this code is ugly, but it's meant to be a very quick and
            // dirty fix...
            $bwinf_data = $bwinf_user[0];
            // Emulate data sent by PMS
            $owner = [
                'firstName' => $bwinf_data->firstName,
                'lastName' => $bwinf_data->lastName,
                'nickName' => $bwinf_data->nickName,
                'eMail' => $bwinf_data->eMail,
                'gender' => ($bwinf_data->gender == 1) ? 'm' : 'f',
                'participations' => [[
                    'competitionName' => 'Jugendwettbewerb Informatik 2017',
                    'competitionId' => 13,
                    'participationId' => -1,
                    'grade' => $bwinf_data->grade
                    ]],
                'userID' => $bwinf_data->ID,
                'dateOfBirth' => '2002-01-01',
                'schoolId' => $bwinf_data->schoolId ? $bwinf_data->schoolId : -1,
                'teacherUserId' => -1,
                'teacherFirstName' => 'Teacher',
                'teacherLastName' => 'Unknown',
                'userType' => $bwinf_data->teacher == 'true' ? 't' : 's'
                ];
            $auth = [
                'provider' => 'pms',
                'uid' => array_get($owner, 'userID'),
                'login' => array_get($owner, 'nickName', array_get($owner, 'eMail')),
                'access_token' => '',
                'refresh_token' => '',
                'email' => array_get($owner, 'eMail'),
                'birthday' => array_get($owner, 'dateOfBirth'),
                'first_name' => array_get($owner, 'firstName'),
                'last_name' => array_get($owner, 'lastName'),
                'language' => 'de',
                'pms_info' => $owner,
                ];
            $connection = new AuthConnection($auth);
            $connection->active = true;
            if(isset($auth['email']) && Email::where('email', $auth['email'])->first()) {
                return;
            }
            if(!isset($auth['login'])) {
                $auth['login'] = $auth['uid'];
            }
            $user = User::create($auth);
            $user->auth_connections()->save($connection);
            if(isset($auth['email'])) {
                $user->emails()->save(new Email([
                    'role' => 'primary',
                    'email' => $auth['email'],
                    'verified' => true
                ]));
            }
            $user->obsolete_passwords()->save(new ObsoletePassword([
                'salt' => '',
                'password' => $bwinf_data->password,
                'type' => 'sha512'
                ]));
            AuthConnector::addBadge($user, $auth);

            $query = $this->createModel()->newQuery();

            if(strpos($credentials['login'], '@') === false) {
                $query->where('login', $credentials['login']);
            } else if($email = \App\Email::where('email', $credentials['login'])->first()) {
                $query->where('id', $email->user_id);
            } else return;

            return $query->first();
        }
        return;
    }


    public function validateCredentials(UserContract $user, array $credentials) {
        return UserPassword::check($user, $credentials['password']);
    }

}
