<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrivewayTypeUserProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driveway_type_user_profile', function (Blueprint $table) {
            $table->bigInteger('driveway_type_id')->unsigned()->nullable()->index();
            $table->bigInteger('user_profile_id')->unsigned()->nullable()->index();
            $table->timestamps();

            $table->foreign('driveway_type_id')->references('id')->on('driveway_types');
            $table->foreign('user_profile_id')->references('id')->on('user_profiles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driveway_type_user_profile');
    }
}
