<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaintenanceItemOutdoorSpaceTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_item_outdoor_space_type', function (Blueprint $table) {
            $table->bigInteger('outdoor_space_type_id')->unsigned()->nullable()->index('space_type_id');
            $table->bigInteger('maintenance_item_id')->unsigned()->nullable()->index('maint_item_id');
            $table->timestamps();

            $table->foreign('outdoor_space_type_id', 'outdoor_space_type_maint_item')
                ->references('id')->on('outdoor_space_types')->onDelete('cascade');
            $table->foreign('maintenance_item_id')
                ->references('id')->on('maintenance_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outdoor_space_type_maintenance_item');
    }
}
