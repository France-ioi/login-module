<?php
namespace App\LoginModule\Bebras;


use DB;
use App\LoginModule\Bebras\Data;
use App\User;
use App\Email;
use App\ObsoletePassword;

class Migrator {

    const CHUNK_SIZE = 100;

    protected $command;
    protected $connection;


    public function __construct($command, $connection) {
        $this->command = $command;
        $this->connection = $connection;

    }


    public function run($id) {
        $offset = 0;
        while(count($users = Data::queryUsers($this->connection, $offset, self::CHUNK_SIZE))) {
            foreach($users as $user_data) {
                DB::transaction(function() use ($user_data) {
                    if($user = $this->syncUser($user_data)) {
                        $this->syncEmail($user, 'primary', $user_data['primary_email'], $user_data['primary_email_verified']);
                        $this->syncEmail($user, 'secondary', $user_data['secondary_email'], $user_data['secondary_email_verified']);
                        $this->syncPassword($user, $user_data);
                    }
                });
            }
            $offset += self::CHUNK_SIZE;
        }
    }


    private function syncUser($user_data) {
        if($user_data['login_module_id']) {
            $user = User::find($user_data['login_module_id']);
            if(!$user) {
                $this->command->error('externalID #'.$user_data['login_module_id'].' defined, but user not found');
                return null;
            }
            $user->fill($user_data);
            $user->save();
            return $user;
        }

        if($email = Email::where('email', $user_data['primary_email'])->orWhere('email', $user_data['secondary_email'])->first()) {
            $email->user->fill($user_data);
            $email->user->save();
            Data::updateUserExternalId($this->connection, $user_data['bebras_id'], $email->user->id);
            return $email->user;
        }

        $user = User::create($user_data);
        $user->save();
        Data::updateUserExternalId($this->connection, $user_data['bebras_id'], $user->id);
        return $user;
    }


    private function syncEmail($user, $role, $email, $verified) {
        if($row = $user->emails()->where('role', $role)->first()) {
            $row->fill([
                'email' => $email,
                'verified' => $verified
            ]);
            $row->save();
        } else {
            $row = new Email([
                'email' => $email,
                'role' => $role,
                'verified' => $verified
            ]);
            $user->emails()->save($row);
        }
    }


    private function syncPassword($user, $user_data) {
        if(!empty($user_data['password']) &&
            !$user->obsolete_passwords()->where('password', $user_data['password'])->first()) {
            $user->obsolete_passwords()->save(new ObsoletePassword($user_data));
        }
    }

}