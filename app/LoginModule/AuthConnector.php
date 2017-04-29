<?php

namespace App\LoginModule;

use Illuminate\Support\Facades\Hash;
use App\User;
use App\Email;
use App\Badge;
use App\AuthConnection;
use App\PmsAdminBadge;
use Auth;

class AuthConnector
{


    static function connect($auth) {
        if($connection = self::findConnection($auth)) {
            $user = $connection->user;
            Auth::login($user);
            $connection->active = true;
            $connection->save();
            if(!isset($auth['login'])) {
                $auth['login'] = $auth['uid'];
            }
            if($auth['provider'] == 'pms') {
                $user->update($auth);
            }
        } else {
            $connection = new AuthConnection($auth);
            $connection->active = true;
            if(Auth::check()) {
                $user = Auth::user();
                $user->auth_connections()->save($connection);
            } else {
                if(isset($auth['email']) && Email::where('email', $auth['email'])->first()) {
                    return false;
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
                Auth::login($user);
            }
        }
        self::addBadge($user, $auth);
        return $user;
    }

    static function addBadge($user, $auth) {
        if($auth['provider'] == 'pms') {
            // TODO :: optimize queries
            // Handle PMS school and participation information
            $authinfo = $auth['pms_info'];
            $badges = array();
            $teacherBadges = array();

            // Fetch manager badges if we have any
            $myAdminBadges = PmsAdminBadge::where('pms_id', $authinfo['userID'])->get();
            // Handle PMS school and participation information
            foreach($myAdminBadges as $myBadge) {
                $badges[] = $myBadge['badge'];
            }

            if(isset($authinfo['teacherUserId'])) {
                $badges[] = 'teacher://pms.bwinf.de/member/teacher_'.$authinfo['teacherUserId'].'/'.$authinfo['teacherFirstName'].' '.$authinfo['teacherLastName'];
                $teacherBadges[] = 'teacher://pms.bwinf.de/manager/teacher_'.$authinfo['teacherUserId'].'/'.$authinfo['teacherFirstName'].' '.$authinfo['teacherLastName'];
            }

            // Make badge for school
            if(isset($authinfo['schoolId'])) {
                // Dummy school name, TODO :: fetch school names
                $badges[] = 'school://pms.bwinf.de/member/school_'.$authinfo['schoolId'].'/school_'.$authinfo['schoolId'];
            }

            // Make badges for participations
            $maxGrade = -1000;
            if(isset($authinfo['participations'])) {
                foreach($authinfo['participations'] as $participation) {
                    // TODO :: Get the actual grade name
                    $badges[] = 'competition://pms.bwinf.de/member/competition_'.$participation['competitionId'].'/'.$participation['competitionName'].'/grade_'.$participation['grade'].'/'.$participation['competitionName'].' - Grade '.$participation['grade'];
                    $maxGrade = max($maxGrade, $participation['grade']);
                }
            }
            if($maxGrade > -1000) {
                // TODO :: Get the actual grade name
                $badges[] = 'grade://pms.bwinf.de/member/grade_'.$maxGrade.'/Grade '.$maxGrade;
            }

            // Add badges
            foreach($badges as $url) {
                if(!$user->badges()->where('url', $url)->first()) {
                    $user->badges()->save(new Badge([
                        'url' => $url
                    ]));
                }
            }

            // Store badges for teachers
            // TODO :: do we still have multiple badges?
            if(isset($authinfo['teacherUserId'])) {
                foreach($teacherBadges as $url) {
                    if(!PmsAdminBadge::where('pms_id', $authinfo['teacherUserId'])->where('badge', $url)->first()) {
                        PmsAdminBadge::create([
                            'pms_id' => $authinfo['teacherUserId'],
                            'badge' => $url,
                        ]);
                    }
                }
            }
        }
    }


    static function findConnection($auth) {
        // replace old google id
        if($auth['provider'] == 'google' && !empty($auth['uid_old'])) {
            if($connection = AuthConnection::where('uid', $auth['uid_old'])->where('provider', $auth['provider'])->first()) {
                $connection->uid = $auth['uid'];
                return $connection;
            }
        }

/*
TODO: discuss legacy pms data
        if($auth['provider'] == 'pms') {
            $legacy_connections = AuthConnection::whereIn('uid', [$auth['uid'], $auth['login'], $auth['email']])->where('provider', $auth['provider'])->get();
            dd($legacy_connections);
            if(count($legacy_connections) > 0) {
                $connection = $legacy_connections->pop();
                $connection->uid = $auth['uid'];
                foreach($legacy_connections as $legacy_connection) {
                    $legacy_connection->delete();
                }
                return $connection;
            }
            return null;
        }

        return AuthConnection::where('uid', $auth['uid'])->where('provider', $auth['provider'])->first();
*/

        $connection = AuthConnection::where('uid', $auth['uid'])->where('provider', $auth['provider'])->first();
        if($auth['provider'] == 'pms' && !$connection) {
            // Check for a legacy connection
            $connection = AuthConnection::where('uid', $auth['login'])->where('provider', $auth['provider'])->first();
            if(!$connection) {
                $connection = AuthConnection::where('uid', $auth['email'])->where('provider', $auth['provider'])->first();
            }

            if($connection) {
                // Found connection, create a new connection with the UID
                $uid_connection = new AuthConnection($auth);
                $uid_connection->active = true;
                $user = $connection->user;
                $user->auth_connections()->save($uid_connection);
            }
        }
        return $connection;
    }


    static function disconnect($provider) {
        if(Auth::check()) {
            Auth::user()->auth_connections()->where('provider', $provider)->delete();
        }
    }

}
