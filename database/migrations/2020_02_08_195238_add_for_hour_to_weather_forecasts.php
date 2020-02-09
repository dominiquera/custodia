<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForHourToWeatherForecasts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('weather_forecasts', function (Blueprint $table) {
            $table->tinyInteger('for_hour')->after('for_date');
            $table->dropUnique(['for_date','city','state']);
            $table->unique(['for_date','for_hour','city','state']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('weather_forecasts', function (Blueprint $table) {
            $table->dropUnique(['for_date','for_hour','city','state']);
            $table->unique(['for_date','city','state']);
            $table->dropColumn('for_hour');
        });
    }
}
