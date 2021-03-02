<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaregiverInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caregiver_infos', function (Blueprint $table) {
            $table->id();
            
            $table->integer('user_id');
            $table->integer('caregiver_id');
            $table->string('intials')->nullable();
            $table->integer('caregiver_gender_id')->nullable();
            $table->integer('caregiver_code')->nullable();
            $table->string('alternate_caregiver_code')->nullable();
            $table->integer('time_attendance_pin')->nullable();
            $table->json('mobile')->nullable();
            $table->json('ethnicity')->nullable();
            $table->string('country_of_birth')->nullable();
            $table->string('employee_type')->nullable();
            $table->json('marital_status')->nullable();
            $table->string('dependents')->nullable();
            $table->json('status')->nullable();
            $table->string('employee_id')->nullable();
            $table->string('application_date')->nullable();
            $table->string('hire_date')->nullable();
            $table->string('rehire_date')->nullable();
            $table->string('first_work_date')->nullable();
            $table->string('last_work_date')->nullable();
            $table->integer('registry_number')->nullable();
            $table->string('registry_checked_date')->nullable();
            $table->json('referral_source')->nullable();
            $table->string('referral_person')->nullable();
            $table->json('notification_preferences')->nullable();
            $table->json('caregiver_offices')->nullable();
            $table->json('inactive_reason_detail')->nullable();
            $table->string('terminated_date')->nullable();
            $table->string('professional_licensenumber')->nullable();
            $table->string('npi_number')->nullable();
            $table->string('signed_payroll_agreement_date')->nullable();
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
        Schema::dropIfExists('caregiver_infos');
    }
}
