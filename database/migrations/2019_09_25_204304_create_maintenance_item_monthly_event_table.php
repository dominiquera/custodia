<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaintenanceItemMonthlyEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_item_monthly_event', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('maintenance_item_id')->unsigned()->nullable()->index();
            $table->bigInteger('monthly_event_id')->unsigned()->nullable()->index();
            $table->timestamps();

            $table->foreign('maintenance_item_id')->references('id')->on('maintenance_items')->onDelete('cascade');
            $table->foreign('monthly_event_id')->references('id')->on('monthly_events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_item_monthly_event');
    }
}
