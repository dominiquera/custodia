<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveMaintenanceItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('maintenance_items', function (Blueprint $table) {
            $table->dropForeign('maintenance_items_interval_id_foreign');
            $table->dropColumn('interval_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('maintenance_items', function (Blueprint $table) {
            //
        });
    }
}
