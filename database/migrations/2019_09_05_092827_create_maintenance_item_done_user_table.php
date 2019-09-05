<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaintenanceItemDoneUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_item_done_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('maintenance_item_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_item_done_user');
    }
}
