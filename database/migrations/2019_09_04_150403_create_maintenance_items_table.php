<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaintenanceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('section_id')->unsigned()->nullable()->index();
            $table->bigInteger('featured_image_id')->nullable()->unsigned();
            $table->bigInteger('interval_id')->unsigned();
            $table->bigInteger('weather_trigger_type_id')->nullable()->unsigned();
            $table->string('title');
            $table->double('points');
            $table->boolean('mobility_priority')->default(false);
            $table->longText('summary');
            $table->longText('cautions')->nullable();
            $table->timestamps();

            $table->foreign('section_id')->references('id')->on('sections');
            $table->foreign('featured_image_id')->references('id')->on('images');
            $table->foreign('interval_id')->references('id')->on('intervals');
            $table->foreign('weather_trigger_type_id')->references('id')->on('weather_trigger_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_items');
    }
}
