<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScoreFactorToMaintenanceItemOutdoorSpaceType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('maintenance_item_outdoor_space_type', function (Blueprint $table) {
            $table->tinyInteger('score_factor')
                  ->nullable(false)
                  ->default(1)
                  ->after('maintenance_item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('maintenance_item_outdoor_space_type', function (Blueprint $table) {
            $table->dropColumn('score_factor');
        });
    }
}
