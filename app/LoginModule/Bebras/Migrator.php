<?php
namespace App\LoginModule\Bebras;


use DB;
use App\LoginModule\Bebras\Data;
use App\User;
use App\Email;

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
        if($email = Email::where('email', $user_data['primary_email'])->first()) {
            $email->user->fill($user_data);
            $email->user->save();
        } else {
            $user = new User($user_data);
            $user->id = $user_data['id'];
            $email->user()->save($user);
        }
        return $email->user;
    }


    private function syncEmail($user, $role, $email, $verified) {
        if($email = $user->emails()->where('role', $role)->first()) {
            $email->fill([
                'email' => $email,
                'verified' => $verified
            ]);
            $email->save();
        } else {
            $email = new Email([
                'email' => $email,
                'role' => $role,
                'verified' => $verified
            ]);
            $user->emails()->save($email);
        }
    }


    private function syncPassword($user, $user_data) {
        if(!empty($user_data['password']) &&
            !$user->obsolete_passwords()->where('password', $user_data['password'])->first()) {
            $user->obsolete_passwords()->save(new ObsoletePassword($user_data));
        }
    }

}