<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_histories', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->integer('sender_id')->nullable();
            $table->integer('receiver_id')->nullable();
            $table->integer('request_id')->nullable();
            $table->string('model_type');
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
        Schema::dropIfExists('notification_histories');
    }
}
