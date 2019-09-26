<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutdoorSpaceTypeUserProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outdoor_space_type_user_profile', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('outdoor_space_type_id')->unsigned()->nullable();
            $table->bigInteger('user_profile_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('outdoor_space_type_id')->references('id')->on('outdoor_space_types');
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
        Schema::dropIfExists('outdoor_space_type_user_profile');
    }
}
