<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index('user_id');
            $table->integer('device_id')->unique();
            $table->enum('device_type', ['1', '2', '3'])->comment('1=BloodPressure,2=Glucometer,3=Digital Weight Machine');
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
        Schema::dropIfExists('user_devices');
    }
}
