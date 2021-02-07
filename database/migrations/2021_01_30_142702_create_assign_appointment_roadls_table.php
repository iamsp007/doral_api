<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignAppointmentRoadlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assign_appointment_roadls', function (Blueprint $table) {
            $table->id();
            $table->integer('appointment_id');
            $table->integer('patient_request_id');
            $table->string('referral_type')->default('LAB');
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
        Schema::dropIfExists('assign_appointment_roadls');
    }
}
