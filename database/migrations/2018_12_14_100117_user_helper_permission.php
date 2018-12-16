<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserHelperPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        app()['cache']->forget('spatie.permission.cache');
        Permission::create(['name' => 'admin.user_helper']);

        // create roles and assign created permissions
        $role = Role::where('name', 'admin')->first();
        if($role) {
            $role->givePermissionTo('admin.user_helper');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
