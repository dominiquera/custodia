<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScoreFactorToHomeTypeMaintenanceItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('home_type_maintenance_item', function (Blueprint $table) {
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
        Schema::table('home_type_maintenance_item', function (Blueprint $table) {
            $table->dropColumn('score_factor');
        });
    }
}
