<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrivewayTypeMaintenanceItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driveway_type_maintenance_item', function (Blueprint $table) {
            $table->bigInteger('driveway_type_id')->unsigned()->nullable()->index();
            $table->bigInteger('maintenance_item_id')->unsigned()->nullable()->index();
            $table->timestamps();

            $table->foreign('driveway_type_id')->references('id')->on('driveway_types');
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
        Schema::dropIfExists('driveway_type_maintenance_item');
    }
}
