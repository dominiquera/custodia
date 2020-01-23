<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIgnoreUntilToMaintenanceItemIgnoredUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('maintenance_item_ignored_user', function (Blueprint $table) {
            $table->date('ignore_until');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('maintenance_item_ignored_user', function (Blueprint $table) {
            $table->dropColumn('ignore_until');
        });
    }
}
