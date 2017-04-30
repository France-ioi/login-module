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
            $connection->refresh_token = $auth['refresh_token'];
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
        // TODO :: should probably be in PMSProvider rather than here
        if($auth['provider'] == 'pms') {
            // TODO :: optimize queries
            // Handle PMS school and participation information
            $authinfo = $auth['pms_info'];
            $badges = array();
            $teacherBadges = array();

            // Fetch manager badges if we have any
            // TODO :: do we still need that?
            /*$myAdminBadges = PmsAdminBadge::where('pms_id', $authinfo['userID'])->get();
            foreach($myAdminBadges as $myBadge) {
                $badges[] = $myBadge['badge'];
            }*/

            // Get teacher information
            if($authinfo['userType'] == 't') {
                $badges[] = 'teacher://pms.bwinf.de/manager/teacher_'.$authinfo['userID'].'/'.$authinfo['firstName'].' '.$authinfo['lastName'];
            } elseif(isset($authinfo['teacherUserId'])) {
                $badges[] = 'teacher://pms.bwinf.de/member/teacher_'.$authinfo['teacherUserId'].'/'.$authinfo['teacherFirstName'].' '.$authinfo['teacherLastName'];
                //$teacherBadges[] = 'teacher://pms.bwinf.de/manager/teacher_'.$authinfo['teacherUserId'].'/'.$authinfo['teacherFirstName'].' '.$authinfo['teacherLastName'];
            }

            // Make badge for school
            if(isset($authinfo['schoolId'])) {
                // Dummy school name, TODO :: fetch school names
                if($authinfo['userType'] == 't') {
                    // We get an array of schoolIds
                    foreach($authinfo['schoolId'] as $schoolId) {
                        $badges[] = 'school://pms.bwinf.de/member/school_'.$schoolId.'/school_'.$schoolId;
                    }
                } else {
                    $badges[] = 'school://pms.bwinf.de/member/school_'.$authinfo['schoolId'].'/school_'.$authinfo['schoolId'];
                }
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
            // TODO :: do we still need that?
            /*if(isset($authinfo['teacherUserId'])) {
                foreach($teacherBadges as $url) {
                    if(!PmsAdminBadge::where('pms_id', $authinfo['teacherUserId'])->where('badge', $url)->first()) {
                        PmsAdminBadge::create([
                            'pms_id' => $authinfo['teacherUserId'],
                            'badge' => $url,
                        ]);
                    }
                }
            }*/
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

        if($auth['provider'] == 'pms') {
            // PMS connections were formerly identified by nickname/email
            // now they're identified by userID
            $legacy_connections = AuthConnection::whereIn('uid', [$auth['uid'], $auth['login'], $auth['email']])->where('provider', $auth['provider'])->get();
            if(count($legacy_connections) > 0) {
                $connection = $legacy_connections->pop();
                if($connection->uid != $auth['uid']) {
                    // Update old connection to use the userID
                    $connection->uid = $auth['uid'];
                    // not needed as it is currently saved in connect(), but it could change
                    $connection->save();
                }
                // Delete old connections
                foreach($legacy_connections as $legacy_connection) {
                    $legacy_connection->delete();
                }
                return $connection;
            }
            return null;
        }

        return AuthConnection::where('uid', $auth['uid'])->where('provider', $auth['provider'])->first();
    }


    static function disconnect($provider) {
        if(Auth::check()) {
            Auth::user()->auth_connections()->where('provider', $provider)->delete();
        }
    }

}
