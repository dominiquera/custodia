<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('role_id')->default(2)->unsigned()->index();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique()->nullable()->index();
            $table->string('google_auth_id')->unique()->nullable()->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->default(bcrypt('secret'));
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
