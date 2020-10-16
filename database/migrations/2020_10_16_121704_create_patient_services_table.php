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
            $table->foreignId('patient_id')->index('patient_id');
            $table->foreignId('service_id')->index('service_id');
            $table->foreignId('company_id')->index('compnay_id');
            $table->foreignId('employee_id')->index('employee_id');
            $table->string('comment');            
            $table->dateTime('request_date');
            $table->enum('status', ['active', 'rejected', 'accept']);
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
