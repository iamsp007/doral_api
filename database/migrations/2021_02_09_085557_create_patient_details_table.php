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
            $table->integer('doral_id');
            $table->integer('agency_id');
            $table->integer('office_id');
            $table->integer('patient_id');
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->date('birth_date');
            $table->enum('gender', ['1', '2','3'])->comment('1=male,2=female,3=other');
            $table->integer('priority_code');
            $table->date('service_request_start_date');
            $table->integer('admission_id');
            $table->integer('medica_id_number');
            $table->integer('medicare_number');
            $table->integer('ssn');
            $table->foreignId('payer_id')->index('payer_id');

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
