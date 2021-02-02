<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientMdFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_md_forms', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('appointment_id');
            $table->integer('patient_id');
            // Information Field
            $table->string('physical_examination_report')->nullable();
            $table->string('authorize_name')->nullable();
            $table->string('employee_signature')->nullable();
            $table->string('patient_fname')->nullable();
            $table->string('patient_lname')->nullable();
            $table->string('patient_gender')->nullable();
            $table->date('patient_dob')->nullable();
            $table->date('patient_doe')->nullable();
            $table->string('patient_ssn')->nullable();
            $table->string('patient_email')->nullable();
            $table->string('patient_marital_status')->nullable();

            // physician examination
            $table->string('physician_examination')->nullable();
            $table->string('physician_condition')->nullable();
            $table->string('physician_head')->nullable();
            $table->string('physician_symptoms')->nullable();
            $table->string('physician_Weakness')->nullable();

            // Laboratory Results
            $table->string('laboratory_test')->nullable();
            $table->date('laboratory_date_performed')->nullable();
            $table->string('laboratory_results')->nullable();
            $table->string('laboratory_lab_value')->nullable();
            $table->enum('laboratory_individual',['0','1'])->default('1')->nullable();
            $table->string('laboratory_individual_work')->nullable();
            $table->enum('laboratory_not_physician',['0','1'])->default('1')->nullable();
            $table->string('laboratory_not_physician_work')->nullable();
            $table->string('laboratory_physician_name')->nullable();
            $table->string('laboratory_physician_license_no')->nullable();
            $table->string('laboratory_physician_stamp')->nullable();
            $table->string('laboratory_physician_signature')->nullable();
            $table->date('laboratory_physician_date_signed')->nullable();
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
        Schema::dropIfExists('patient_md_forms');
    }
}
