<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->integer('visitor_id');
            $table->string('visit_date');
            $table->string('is_missed_visit');
            $table->json('patient_detail')->nullable();
            $table->json('caregiver_detail')->nullable();
            $table->json('schedule_time_detail')->nullable();
            $table->json('ttot_detail')->nullable();
            $table->json('verification_detail')->nullable();
            $table->json('timesheet_detail')->nullable();
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
        Schema::dropIfExists('visitors');
    }
}
