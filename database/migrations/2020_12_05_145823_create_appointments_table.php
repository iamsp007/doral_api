<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->dateTime('book_datetime');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->foreignId('booked_user_id')->index('booked_user_id');
            $table->foreignId('patient_id')->index('patient_id');
            $table->foreignId('provider1')->index('provider1')->comment('MA / PA');
            $table->foreignId('provider2')->index('provider2')->comment('NP');
            $table->foreignId('service_id')->index('service_id');
            $table->string('Note', 500)->nullable();
            $table->string('appointment_url', 500);
            $table->enum('status', ['open','running', 'completed', 'cancel', 'reject'])->default('open');
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
        Schema::dropIfExists('appointments');
    }
}
