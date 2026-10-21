<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * spatie/laravel-permission v1 -> v6: roles and permissions get a guard_name,
 * user_has_* tables are replaced by the polymorphic model_has_* tables.
 */
class UpgradePermissionTables extends Migration
{
    const USER_MODEL = 'App\\User';

    public function up()
    {
        if(!Schema::hasTable('user_has_roles')) {
            // database created with the v6 schema already
            return;
        }

        foreach(['roles', 'permissions'] as $name) {
            Schema::table($name, function (Blueprint $table) use ($name) {
                $table->string('guard_name')->default('web')->after('name');
                $table->dropUnique($name.'_name_unique');
                $table->unique(['name', 'guard_name']);
            });
        }

        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->unsignedInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->index(['model_id', 'model_type'], 'model_has_permissions_model_id_model_type_index');
            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');
            $table->primary(['permission_id', 'model_id', 'model_type'], 'model_has_permissions_permission_model_type_primary');
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->index(['model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');
            $table->primary(['role_id', 'model_id', 'model_type'], 'model_has_roles_role_model_type_primary');
        });

        DB::insert(
            'INSERT INTO model_has_permissions (permission_id, model_type, model_id) SELECT permission_id, ?, user_id FROM user_has_permissions',
            [self::USER_MODEL]
        );
        DB::insert(
            'INSERT INTO model_has_roles (role_id, model_type, model_id) SELECT role_id, ?, user_id FROM user_has_roles',
            [self::USER_MODEL]
        );

        Schema::drop('user_has_permissions');
        Schema::drop('user_has_roles');
    }

    public function down()
    {
        // Converts back to the v1 schema (only usable with the pre-upgrade code)
        Schema::create('user_has_permissions', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('permission_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->primary(['user_id', 'permission_id']);
        });

        Schema::create('user_has_roles', function (Blueprint $table) {
            $table->integer('role_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->primary(['role_id', 'user_id']);
        });

        DB::insert(
            'INSERT INTO user_has_permissions (user_id, permission_id) SELECT model_id, permission_id FROM model_has_permissions WHERE model_type = ?',
            [self::USER_MODEL]
        );
        DB::insert(
            'INSERT INTO user_has_roles (role_id, user_id) SELECT role_id, model_id FROM model_has_roles WHERE model_type = ?',
            [self::USER_MODEL]
        );

        Schema::drop('model_has_permissions');
        Schema::drop('model_has_roles');

        foreach(['roles', 'permissions'] as $name) {
            Schema::table($name, function (Blueprint $table) use ($name) {
                $table->dropUnique($name.'_name_guard_name_unique');
                $table->dropColumn('guard_name');
                $table->unique('name');
            });
        }
    }
}
