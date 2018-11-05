<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\BadgeApi;

class CreateBadgeApisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('badge_apis', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
            $table->text('url');
            $table->boolean('auth_enabled')->default(false);
        });
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->integer('badge_api_id')->unsigned()->nullable()->index()->after('badge_url');
            $table->foreign('badge_api_id')->references('id')->on('badge_apis')->onDelete('cascade');
        });

        App\Client::get()->each(function($client) {
            if(!$client->badge_url) {
                return;
            }
            $api = BadgeApi::where('url', $client->badge_url)->first();
            if(!$api) {
                $api = BadgeApi::create([
                    'url' => $client->badge_url,
                    'name' => $client->name.' badge API'
                ]);
            }
            $client->badge_api_id = $api->id;
            $client->save();
        });

        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->dropColumn('badge_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->string('badge_url')->after('badge_api_id');
        });

        App\Client::get()->each(function($client) {
            if(!$client->badge_api_id) {
                return;
            }
            $client->badge_url = $client->badgeApi->url;
            $client->save();
        });

        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->dropForeign('oauth_clients_badge_api_id_foreign');
            $table->dropColumn('badge_api_id');
        });
        Schema::dropIfExists('badge_apis');
    }
}
