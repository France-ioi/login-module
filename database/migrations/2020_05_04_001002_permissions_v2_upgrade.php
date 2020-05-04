<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class PermissionsV2Upgrade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('permissions', function (Blueprint $table) {
            $table->string('guard_name');
        });
        \DB::statement('UPDATE `permissions` SET guard_name = \'web\'');

        Schema::table('roles', function (Blueprint $table) {
            $table->string('guard_name');
        });        
        \DB::statement('UPDATE `roles` SET guard_name = \'web\'');


        Schema::table('user_has_permissions', function (Blueprint $table) {
            $table->string('model_type');
        });
        \DB::statement('UPDATE `user_has_permissions` SET model_type = \'App\\\User\'');
        Schema::table('user_has_permissions', function (Blueprint $table) {
            \DB::statement('
                ALTER TABLE `user_has_permissions`
                ADD PRIMARY KEY `user_id_permission_id_model_type` (`user_id`, `permission_id`, `model_type`),
                DROP INDEX `PRIMARY`
            ');
            $table->index(['user_id', 'model_type'], 'user_has_permissions_user_id_model_type_index');                
        });        

        

        Schema::table('user_has_roles', function (Blueprint $table) {
            $table->string('model_type');
        });
        \DB::statement('UPDATE `user_has_roles` SET model_type = \'App\\\User\'');
        Schema::table('user_has_roles', function (Blueprint $table) {
            \DB::statement('
                ALTER TABLE `user_has_roles`
                ADD PRIMARY KEY `role_id_user_id_model_type` (`role_id`, `user_id`, `model_type`),
                DROP INDEX `PRIMARY`;
            ');
            $table->index(['user_id', 'model_type'], 'user_has_roles_user_id_model_type_index');                
        });        


        
        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('guard_name');
        });
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('guard_name');
        });        

        Schema::table('user_has_permissions', function (Blueprint $table) {
            \DB::statement('
                ALTER TABLE `user_has_permissions`
                ADD PRIMARY KEY `user_id_permission_id` (`user_id`, `permission_id`),
                DROP INDEX `PRIMARY`
            ');
        });
        Schema::table('user_has_permissions', function (Blueprint $table) {
            $table->dropColumn('model_type');
            $table->dropIndex('user_has_permissions_user_id_model_type_index');
        });


        Schema::table('user_has_roles', function (Blueprint $table) {
            \DB::statement('
                ALTER TABLE `user_has_roles`
                ADD PRIMARY KEY `role_id_user_id` (`role_id`, `user_id`),
                DROP INDEX `PRIMARY`
            ');
        });
        Schema::table('user_has_roles', function (Blueprint $table) {
            $table->dropColumn('model_type');
            $table->dropIndex('user_has_roles_user_id_model_type_index');
        });


        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));        
    }
}
