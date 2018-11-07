<?php

/*

Console script:
1. insert all accounts from both login modules into one db
2. combine accounts with same login/emails into groups.
    group conditions:  same login values OR same primary/secondary email values

On login:
>1. if user in a group of 2 users and both users have same passwords then automatically merge badges, delete another user and delete group
replaced with:
1. if logged user A in a group C and another users D in a group C have same password then do:
    1.1. transfer badges from D to A
    1.2. delete D
    1.3. remove C if it contain only A

After login:



2. if user in a group of 2 users and passwords are different then offer to merge:
    2.1. merge declined: remove logged user from group, delete group, force to re-validate login/email (a)
    2.2. merge accepted:
        2.2.1. if user come with merge token (b) then automatically merge badges, delete another user and delete group
        2.2.2. if not then ask to logout, then redirect to login with merge token
3. if user in a group of more than 2 users then force to re-validate login/email
    3.1. if user change login/pwd to unique then remove user from group, If the group contain one account left then delete group

(a) re-validate login/email  - redirect to profile page with message, ask to enter unique value
(b) merge token - crypted token with merge request

*/



namespace App\LoginModule\Migrators\Merge;

use App\User;
use App\Badge;
use App\LoginModule\UserPassword;

class Group
{

    public static function mergingRequired($user) {
        if(!is_null($user->merge_group_id)) {
            $group_users = User::where('merge_group_id', $user->merge_group_id)->where('id', '<>', $user->id)->get();
            if(count($group_users) < 2) {
                return true;
            }
            self::reqireRevalidation($user, $group_users);
        }
        return false;
    }


    public static function reqireRevalidation($user, $group_users) {
        $user->merge_group_id = null;
        $group_users->map(function($group_user) use ($user) {
            if(!is_null($user->login) && $user->login === $group_user->login) {
                $user->login_revalidate_required = true;
            }
            if($user->primary_email_id && $group_user->primary_email_id && $user->primary_email === $group_user->primary_email) {
                $email = $user->emails->where('role', 'primary')->first();
                $email->email_revalidate_required = true;
                $email->save();
            }
            if($user->secondary_email_id && $group_user->secondary_email_id && $user->secondary_email === $group_user->secondary_email) {
                $email = $user->emails->where('role', 'secondary')->first();
                $email->email_revalidate_required = true;
                $email->save();
            }
        });
        if(count($group_users) == 1) {
            $group_users[0]->merge_group_id = null;
            $group_users[0]->save();
        }
        $user->save();
    }


    public static function revalidationRequired($user) {
        return count(self::getRevalidationFields($user)) > 0;
    }


    public static function getRevalidationFields($user) {
        $res = [];
        if($user->login_revalidate_required) {
            $res[] = 'login';
        }
        if($user->primary_email_id && $user->emails->where('role', 'primary')->first()->email_revalidate_required) {
            $res[] = 'primary_email';
        }
        if($user->secondary_email_id && $user->emails->where('role', 'secondary')->first()->email_revalidate_required) {
            $res[] = 'secondary_email';
        }
        return $res;
    }


    public static function create($users) {
        $grouped_user = $users->filter(function($user) {
            return !is_null($user->merge_group_id);
        })->shift();

        $merge_group_id = $grouped_user ? $grouped_user->merge_group_id : $users[0]->id;
        $users->map(function($user) use ($merge_group_id) {
            $user->merge_group_id = $merge_group_id;
            $user->save();
        });
        return $merge_group_id;
    }


    public static function drop($users) {
        $users->map(function($user) {
            $user->merge_group_id = null;
            $user->save();
        });
    }


    public static function reduceByPassword($user, $password) {
        if(!is_null($user->merge_group_id)) {
            $users_left = User::where('merge_group_id', $user->merge_group_id)
            ->where('id', '<>', $user->id)
            ->get()
            ->filter(function($group_user) use ($user, $password){
                if(UserPassword::check($group_user, $password)) {
                    self::mergeUsers($user, $group_user);
                    return false;
                }
                return true;
            });
            if(count($users_left) == 0) {
                $user->merge_group_id = null;
                $user->save();
            }
        }
        return $user;
    }


    public static function mergeUsers($dst_user, $src_user) {
        $src_user->badges
            ->reject(function($src_badge) use ($dst_user) {
                $exist = $dst_user->badges->search(function($dst_badge) {
                    $same_url = $src_badge->url && $dst_badge->url === $src_badge->url;
                    $same_api_id = $src_badge->badge_api_id && $dst_badge->badge_api_id === $src_badge->badge_api_id;
                    return $same_url || $same_api_id;
                });
                if($exist) return true;
            })
            ->map(function($new_badge) use ($dst_user) {
                $new_badge = new Badge(collect($new_badge->getAttributes())->except('id')->toArray());
                $dst_user->badges()->save($new_badge);
            });
        $src_user->delete();
    }


}