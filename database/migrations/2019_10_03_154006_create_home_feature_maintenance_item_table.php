<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHomeFeatureMaintenanceItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('home_feature_maintenance_item', function (Blueprint $table) {
            $table->bigInteger('home_feature_id')->unsigned()->nullable()->index();
            $table->bigInteger('maintenance_item_id')->unsigned()->nullable()->index();
            $table->timestamps();

            $table->foreign('home_feature_id')->references('id')->on('home_features');
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
        Schema::dropIfExists('home_feature_maintenance_item');
    }
}
