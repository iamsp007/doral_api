<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpenVoiceCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('open_voice_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->index('patient_id');
            $table->foreignId('employee_id')->index('employee_id');
            $table->foreignId('role_id')->index('role_id');
            $table->time('call_duration');
            $table->datetime('call_start_time');
            $table->datetime('call_end_time');
            $table->string('open_talk_id', 100);
            $table->text('open_talk_feedback');
            $table->string('call_type', 50);
            $table->string('comment', '500');
            $table->enum('status',['accepted','rejected','hold','pending']);
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
        Schema::dropIfExists('open_voice_calls');
    }
}
