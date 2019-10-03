<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobilityIssueTypeUserProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobility_issue_type_user_profile', function (Blueprint $table) {
            $table->bigInteger('mobility_issue_type_id')->unsigned()->nullable()->index();
            $table->bigInteger('user_profile_id')->unsigned()->nullable()->index();
            $table->timestamps();

            $table->foreign('mobility_issue_type_id')->references('id')->on('mobility_issue_types');
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
        Schema::dropIfExists('mobility_issue_type_user_profile');
    }
}
