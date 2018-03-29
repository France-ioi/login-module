<?php

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
        Permission::create(['name' => 'admin.users.manager']);
        Permission::create(['name' => 'admin.verifications.manager']);
        Permission::create(['name' => 'admin.verifications.verify']);
        Permission::create(['name' => 'admin.clients.manager']);
        Permission::create(['name' => 'admin.domains.manager']);
        Permission::create(['name' => 'admin.lm_instances.manager']);

        // create roles and assign created permissions
        $role_admin = Role::create(['name' => 'admin']);

        $role_superadmin = Role::create(['name' => 'superadmin']);
        $role_superadmin->givePermissionTo(Permission::all());

        // create admin user
        $user = \App\User::where('login', 'admin')->first();
        if($user) {
            $user->syncRoles(['admin', 'superadmin']);
        }
    }
}