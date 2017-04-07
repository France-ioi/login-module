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
            $auth['login'] = $auth['uid'];
            $user->update($auth);
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
                $auth['login'] = $auth['uid'];
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

            // Make badge for school
            if(isset($authinfo['schoolId'])) {
                $schoolPath = 'groups://PMS/schools/school_'.$authinfo['schoolId'].'/';
                $badges[] = $schoolPath.'member';
                $teacherBadges[] = $schoolPath.'manager';
            }

            // Make badges for participations
            if(isset($authinfo['participations'])) {
                foreach($authinfo['participations'] as $participation) {
                    $newBadge = 'groups://PMS/competitions/competition_'.$participation['competitionId'].'/';
                    $newBadge .= isset($authinfo['schoolId']) ? 'school_'.$authinfo['schoolId'].'/' : '';
                    $newBadge .= isset($participation['grade']) ? 'grade_'.$participation['grade'].'/' : '';

                    // Student is member, teacher is manager
                    $teacherBadge = $newBadge . 'manager';
                    $newBadge .= 'member';

                    $badges[] = $newBadge;
                    $teacherBadges[] = $teacherBadge;
                }
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
        if(isset($auth['uid_old']) && $auth['provider'] == 'google') {
            if($connection = AuthConnection::where('uid', $auth['uid_old'])->where('provider', $auth['provider'])->first()) {
                $connection->uid = $auth['uid'];
                $connection->save();
                return $connection;
            }
        }
        return AuthConnection::where('uid', $auth['uid'])->where('provider', $auth['provider'])->first();
    }


    static function disconnect($provider) {
        if(Auth::check()) {
            Auth::user()->auth_connections()->where('provider', $provider)->delete();
        }
    }

}
