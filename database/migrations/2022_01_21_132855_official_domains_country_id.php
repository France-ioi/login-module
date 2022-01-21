<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Country;
use App\OfficialDomain;
use Illuminate\Support\Facades\DB;

class OfficialDomainsCountryId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('official_domains', function (Blueprint $table) {
            $table->integer('country_id')->unsigned();
        });

        $countries = Country::get()->pluck('id', 'code');
        $domains = DB::table('official_domains')->get();
        foreach($domains as $domain) {
            DB::table('official_domains')->where('id', $domain->id)->update(['country_id' => $countries[$domain->country_code]]);
        }

        Schema::table('official_domains', function (Blueprint $table) {
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade')->onUpdate('cascade');                             
            $table->dropColumn('country_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('official_domains', function (Blueprint $table) {
            $table->string('country_code', 2);
        });

        $countries = Country::get()->pluck('code', 'id');
        $domains = DB::table('official_domains')->get();
        foreach($domains as $domain) {
            DB::table('official_domains')->where('id', $domain->id)->update(['country_code' => $countries[$domain->country_id]]);
        }        

        Schema::table('official_domains', function (Blueprint $table) {
            $table->dropForeign('official_domains_country_id_foreign');
            $table->dropColumn('country_id');
        });
    }
}
