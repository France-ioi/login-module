<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // create permissions
        Permission::findOrCreate('admin.users.manager');
        Permission::findOrCreate('admin.verifications.manager');
        Permission::findOrCreate('admin.verifications.verify');
        Permission::findOrCreate('admin.clients.manager');
        Permission::findOrCreate('admin.domains.manager');
        Permission::findOrCreate('admin.lm_instances.manager');

        // create roles and assign created permissions
        $role = Role::findOrCreate('admin');
        $role->syncPermissions(Permission::all());

        // create admin user
        $user = \App\User::where('login', 'admin')->first();
        if($user) {
            $user->syncRoles(['admin']);
        }
    }
}