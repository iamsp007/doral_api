<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientOccupationals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_occupationals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_id');
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('caregiver_code', 20)->nullable();
            $table->string('medicaid_number')->nullable();
            $table->string('medicare_number')->nullable();
            $table->string('ssn')->nullable();
            $table->date('start_date')->nullable();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('county')->nullable();
            $table->string('zip')->nullable();
            $table->string('phone1')->nullable();
            $table->string('phone2')->nullable();
            $table->string('eng_name')->nullable();
            $table->string('eng_addres')->nullable();
            $table->string('emg_phone')->nullable();
            $table->enum('status',['pending','accept','running','completed','reject','finish'])->default('pending');
            $table->integer('service_id')->nullable();
            $table->integer('file_type')->nullable();
            $table->string('email')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('form_id')->nullable();
            $table->string('emg_relationship')->nullable();
            $table->date('cert_start_date')->nullable();
            $table->date('cert_end_date')->nullable();
            $table->date('cert_next_date')->nullable();
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
        Schema::dropIfExists('patient_occupationals');
    }
}
