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
            $table->string('intials');
            $table->integer('caregiver_gender_id');
            $table->integer('caregiver_code');
            $table->string('alternate_caregiver_code');
            $table->integer('time_attendance_pin');
            $table->json('mobile');
            $table->json('ethnicity');
            $table->string('country_of_birth');
            $table->string('employee_type');
            $table->json('marital_status');
            $table->string('dependents');
            $table->json('status');
            $table->string('employee_id');
            $table->string('application_date');
            $table->string('hire_date');
            $table->string('rehire_date');
            $table->string('first_work_date');
            $table->string('last_work_date');
            $table->integer('registry_number');
            $table->string('registry_checked_date');
            $table->json('referral_source');
            $table->string('referral_person');
            $table->json('notification_preferences');
            $table->json('caregiver_offices');
            $table->json('inactive_reason_detail');
            $table->string('TerminatedDate');
            $table->string('professional_licensenumber');
            $table->string('npi_number');
            $table->string('signed_payroll_agreement_date');
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
