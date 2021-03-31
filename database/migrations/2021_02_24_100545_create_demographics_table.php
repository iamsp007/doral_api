<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemographicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demographics', function (Blueprint $table) {
            $table->id();
            $table->string('doral_id')->nullable();
            $table->foreignId('user_id')->index('user_id');
            $table->foreignId('service_id')->index('service_id');
            $table->foreignId('company_id')->index('company_id');
            $table->string('patient_id');
            $table->string('ssn')->nullable();
            $table->string('medicaid_number')->nullable();
            $table->string('medicare_number')->nullable();
            $table->json('accepted_services')->nullable();
            $table->json('address')->nullable();
            $table->string('language')->nullable();
            $table->string('ethnicity')->nullable();
            $table->string('country_of_birth')->nullable();
            $table->string('employee_type')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('status')->nullable();
            $table->json('notification_preferences')->nullable();
            $table->enum('type', ['1', '2'])->comment('1=patient,2=caregiver');
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
        Schema::dropIfExists('demographics');
    }
}
