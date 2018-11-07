<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Badge;
use App\BadgeApi;

class BadgeUrlToBadgeApiId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('badges', function (Blueprint $table) {
            $table->integer('badge_api_id')->unsigned()->nullable()->index()->after('url');
            $table->foreign('badge_api_id')->references('id')->on('badge_apis')->onDelete('cascade');
            $table->dropUnique('badges_url_code_unique');
        });

        $conv = BadgeApi::get()->pluck('id', 'url')->toArray();
        Badge::where('url', '<>', '')->chunk(
            100,
            function($badges) use ($conv) {
                foreach($badges as $badge) {
                    if(isset($conv[$badge->url])) {
                        $badge->badge_api_id = $conv[$badge->url];
                        $badge->url = '';
                        $badge->save();
                    }
                }
            }
        );

        Schema::table('badges', function (Blueprint $table) {
            $table->unique(['url', 'code', 'badge_api_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('badges', function (Blueprint $table) {
            $table->dropUnique('badges_url_code_badge_api_id_unique');
        });

        $conv = BadgeApi::get()->pluck('url', 'id')->toArray();
        Badge::whereNotNull('badge_api_id')->chunk(
            100,
            function($badges) use ($conv) {
                foreach($badges as $badge) {
                    if(isset($conv[$badge->badge_api_id])) {
                        $badge->url = $conv[$badge->badge_api_id];
                        $badge->save();
                    }
                }
            }
        );
        Schema::table('badges', function (Blueprint $table) {
            $table->dropForeign('badges_badge_api_id_foreign');
            $table->dropColumn('badge_api_id');
            $table->unique(['url', 'code']);
        });
    }
}
