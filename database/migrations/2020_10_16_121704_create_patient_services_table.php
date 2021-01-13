<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_referral_id')->index('patient_referral_id');
            $table->foreignId('service_id')->index('service_id');
            $table->foreignId('company_id')->nullable();;
            $table->foreignId('employee_id')->nullable();;
            $table->string('comment')->nullable();;            
            $table->dateTime('request_date')->nullable();;
            $table->enum('status', ['active', 'inactive'])->default('active');
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
        Schema::dropIfExists('patient_services');
    }
}
