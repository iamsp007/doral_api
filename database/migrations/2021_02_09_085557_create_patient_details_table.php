<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_details', function (Blueprint $table) {
            $table->id();
            $table->string('doral_id')->nullable();
            $table->integer('agency_id')->nullable();
            $table->integer('office_id')->nullable();
            $table->integer('patient_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['1', '2','3'])->comment('1=male,2=female,3=other')->nullable();

            $table->string('priority_code')->nullable();
            $table->date('service_request_start_date')->nullable();

            $table->integer('nurse_id')->nullable();
            $table->string('nurse_name')->nullable();

            $table->integer('admission_id')->nullable();
            $table->string('medicaid_number')->nullable();
            $table->string('medicare_number')->nullable();

            $table->string('ssn')->nullable();
            $table->longText('alert')->nullable();

            $table->string('source_admission_id')->nullable();
            $table->string('source_admission_name')->nullable();

            $table->string('team_id')->nullable();
            $table->string('team_name')->nullable();

            $table->string('location_id')->nullable();
            $table->string('location_name')->nullable();

            $table->string('branch_id')->nullable();
            $table->string('branch_name')->nullable();
           
            $table->string('home_phone')->nullable();
            $table->string('phone2')->nullable();
            $table->string('phone2_description')->nullable();
            $table->string('phone3')->nullable();
            $table->string('phone3_description')->nullable();
            $table->string('home_phone_location_address_id')->nullable();
            $table->string('home_phone_location_address')->nullable();
            $table->string('home_phone2_location_address_id')->nullable();
            $table->string('home_phone2_location_address')->nullable();
            $table->string('home_phone3_location_address_id')->nullable();
            $table->string('home_phone3_location_address')->nullable();
            $table->string('direction')->nullable();
            
            $table->string('payer_id')->nullable();
            $table->string('payer_name')->nullable();
            $table->string('payer_coordinator_id')->nullable();
            $table->string('payer_coordinator_name')->nullable();
            $table->string('patient_status_id')->nullable();
            $table->string('patient_status_name')->nullable();
            $table->string('wage_parity')->nullable();
            $table->string('wage_parity_from_date1')->nullable();
            $table->string('wage_parity_to_date1')->nullable();
            $table->string('wage_parity_from_date2')->nullable();
            $table->string('wage_parity_to_date2')->nullable();
            $table->string('primary_language_id')->nullable();
            $table->string('primary_language')->nullable();
            $table->string('secondary_language_id')->nullable();
            $table->string('secondary_language')->nullable();
            $table->enum('status', ['0', '1', '2', '3', '4'])->default('0')->comment('0=pending,1=active,2=inactive,3=reject,4=deactivate');

            $table->date('modified_date')->nullable();
            $table->string('mr_number')->nullable();
            
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
        Schema::dropIfExists('patient_details');
    }
}
