<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVirtualRoomTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('virtual_room', function (Blueprint $table) {
            $table->id();
            $table->integer("appointment_id");
            $table->string("uuid");
            $table->string("meeting_id");
            $table->string("host_id");
            $table->string("host_email");
            $table->string("topic");
            $table->date('start_time');
            $table->string('agenda');
            $table->string('duration',10)->default(30);
            $table->integer('type')->default(2)->comment('1=instant,2=schedule,3=RECURRING,8=FIXED_RECURRING_FIXED');
            $table->longText('start_url')->nullable();
            $table->longText('join_url')->nullable();
            $table->longText('zoom_response')->nullable();
            $table->string('status');
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
        Schema::dropIfExists('virtual_room');
    }
}
