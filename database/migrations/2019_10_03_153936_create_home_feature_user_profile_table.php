<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHomeFeatureUserProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('home_feature_user_profile', function (Blueprint $table) {
            $table->bigInteger('home_feature_id')->unsigned()->nullable();
            $table->bigInteger('user_profile_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('home_feature_id')->references('id')->on('home_features');
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
        Schema::dropIfExists('home_feature_user_profile');
    }
}
