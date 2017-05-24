<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Client;
use App\LoginModule\UserProfile\Verification\Verificator;

class PlatformProfileConfig extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        Client::get()->each(function($client) {
            $fields = json_decode($client->profile_fields, true);
            $require = array_diff($fields, Verificator::ATTRIBUTES);
            $verify = array_intersect($fields, Verificator::ATTRIBUTES);
            $profile_fields = [
                'require' => array_values($require),
                'verify' => array_values($verify)
            ];
            $client->profile_fields = json_encode($profile_fields);
            $client->save();
        });
        */
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->renameColumn('profile_fields', 'user_attributes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*
        Client::get()->each(function($client) {
            $client->user_attributes = array_merge(
                $client->user_attributes['require'],
                $client->user_attributes['verify']
            );
            $client->save();
        });
        */
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->renameColumn('user_attributes', 'profile_fields');
        });
    }
}
