<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaintenanceItemManagementPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('management_plan_user_profile', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->bigInteger('user_profile_id')->unsigned()->nullable()->index();
          $table->bigInteger('management_plan_id')->unsigned()->nullable()->index();
          $table->timestamps();

          $table->foreign('user_profile_id')->references('id')->on('user_profiles')->onDelete('cascade');
          $table->foreign('management_plan_id')->references('id')->on('management_plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_item_management_plan');
    }
}
