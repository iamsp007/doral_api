<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDeviceLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_device_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_device_id')->index('user_device_id');
            $table->string('value');
            $table->string('reading_time');
            $table->enum('level', ['1', '2', '3'])->default('1')->comment('1=normal,2=Glucometer,3=emergency');
            $table->enum('status', ['0', '1'])->default('0')->comment('0=inactive,1=active');
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
        Schema::dropIfExists('user_device_logs');
    }
}
