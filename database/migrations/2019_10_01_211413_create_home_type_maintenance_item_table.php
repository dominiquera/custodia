<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHomeTypeMaintenanceItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('home_type_maintenance_item', function (Blueprint $table) {
            $table->bigInteger('home_type_id')->unsigned()->nullable();
            $table->bigInteger('maintenance_item_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('home_type_id')->references('id')->on('home_types');
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
        Schema::dropIfExists('home_type_maintenance_item');
    }
}
