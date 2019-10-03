<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaintenanceItemMobilityIssueTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_item_mobility_issue_type', function (Blueprint $table) {
            $table->bigInteger('mobility_issue_type_id')->unsigned()->nullable()->index('mob_type_id');
            $table->bigInteger('maintenance_item_id')->unsigned()->nullable()->index('maint_item_id');
            $table->timestamps();

            $table->foreign('mobility_issue_type_id', 'mob_type_maint_item')->references('id')->on('mobility_issue_types');
            $table->foreign('maintenance_item_id')->references('id')->on('maintenance_items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mobility_issue_type_maintenance_item');
    }
}
