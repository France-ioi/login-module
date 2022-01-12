<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $pwd = bcrypt('123123');

        // admin user
        $user = \App\User::create([
            'login' => 'admin',
            'password' => $pwd
        ]);
        $user->emails()->save(new \App\Email([
            'email' => 'admin@admin.admin',
            'role' => 'primary'
        ]));
        $user->syncRoles(['admin']);


        // user with email/pwd
        $user = \App\User::create([
            'password' => $pwd
        ]);
        $user->emails()->save(new \App\Email([
            'email' => 't1@t.t',
            'role' => 'primary'
        ]));
        $user->emails()->save(new \App\Email([
            'email' => 't2@t.t',
            'role' => 'secondary'
        ]));


        // nsi test
        $user = \App\User::create([
            'login' => 'nsi-teacher1',
            'password' => $pwd
        ]);
        $user->emails()->save(new \App\Email([
            'email' => 'nsi-teacher1@test.test',
            'role' => 'primary'
        ]));

        $user = \App\User::create([
            'login' => 'nsi-teacher2',
            'password' => $pwd
        ]);
        $user->emails()->save(new \App\Email([
            'email' => 'nsi-teacher2@test.test',
            'role' => 'primary'
        ]));

        $user = \App\User::create([
            'login' => 'nsi-jury1',
            'password' => $pwd
        ]);
        $user->emails()->save(new \App\Email([
            'email' => 'nsi-jury1@test.test',
            'role' => 'primary'
        ]));        

        $user = \App\User::create([
            'login' => 'nsi-jury2',
            'password' => $pwd
        ]);
        $user->emails()->save(new \App\Email([
            'email' => 'nsi-jury2@test.test',
            'role' => 'primary'
        ]));

        $user = \App\User::create([
            'login' => 'nsi-admin1',
            'password' => $pwd
        ]);
        $user->emails()->save(new \App\Email([
            'email' => 'nsi-admin1@test.test',
            'role' => 'primary'
        ]));        

        // user with login/obsolete pwds
        $user = \App\User::create([
            'login' => 'test2',
            'regular_password' => false
        ]);
        $user->obsoletePasswords()->save(new \App\ObsoletePassword([
            'password' => md5('123123'),
            'salt' => ''
        ]));
        $user->obsoletePasswords()->save(new \App\ObsoletePassword([
            'password' => md5('123'.'123123'),
            'salt' => '123'
        ]));
    }


}
