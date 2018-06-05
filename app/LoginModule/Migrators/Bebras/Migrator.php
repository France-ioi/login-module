<?php
namespace App\LoginModule\Migrators\Bebras;


use DB;
use App\User;
use App\Email;
use App\ObsoletePassword;
use App\VerificationMethod;
use App\Verification;
use App\LoginModule\TeacherDomain;

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
                        $this->syncEmail(
                            $user,
                            'primary',
                            $user_data['primary_email'],
                            $user_data['verifications']['primary_email']
                        );
                        $this->syncEmail(
                            $user,
                            'secondary',
                            $user_data['secondary_email'],
                            $user_data['verifications']['secondary_email']
                        );
                        $this->syncPassword($user, $user_data);
                    }
                });
            }
            $offset += self::CHUNK_SIZE;
        }
    }


    private function syncUser($user_data) {
        unset($user_data['password']);
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
            $user = $email->user;
            $user->fill($user_data);
        } else {
            $user = User::create($user_data);
        }
        $user->save();

        if($user_data['verifications']['role']) {
            $method = VerificationMethod::where('name', 'imported_data')->first();
            $verification = new Verification([
                'user_attributes' => ['role'],
                'status' => 'approved',
                'method_id' => $method->id
            ]);
            $user->verifications()->save($verification);
        }

        $external_user = [
            'externalID' => $user->id,
            'manualAccess' => $user_data['validated'] == 1 && !$user_data['verifications']['primary_email'] ? 1 : 0
        ];
        Data::updateExternalUser($this->connection, $user_data['bebras_id'], $external_user);
        return $user;
    }


    private function syncEmail($user, $role, $email, $verified) {
        if(empty($email)) return;
        if($row = $user->emails()->where('role', $role)->first()) {
            $row->fill([
                'email' => $email,
                //'verified' => $verified
            ]);
            $row->save();
        } else {
            $row = new Email([
                'email' => $email,
                'role' => $role,
                //'verified' => $verified
            ]);
            $user->emails()->save($row);
        }
        if($verified) {
            $method_code = VerificationMethod::where('name', 'email_code')->first();
            $method_domain = VerificationMethod::where('name', 'email_domain')->first();
            $verification = new Verification([
                'user_attributes' => [$role.'_email'],
                'status' => 'approved',
                'method_id' => $method_code->id
            ]);
            $user->verifications()->save($verification);

            if($user->role == 'teacher' && TeacherDomain::verify($user)) {
                $verification = new Verification([
                    'user_attributes' => ['role'],
                    'status' => 'approved',
                    'method_id' => $method_domain->id
                ]);
                $user->verifications()->save($verification);
            }
        }

    }


    private function syncPassword($user, $user_data) {
        if(!empty($user_data['password']) &&
            !$user->obsoletePasswords()->where('password', $user_data['password'])->first()) {
            $user->obsoletePasswords()->save(new ObsoletePassword($user_data));
        }
    }

}