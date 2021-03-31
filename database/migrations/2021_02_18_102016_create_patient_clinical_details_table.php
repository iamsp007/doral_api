<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientClinicalDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_clinical_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->index('patient_id');            
            $table->integer('nursing_visits_due')->nullable();
            $table->enum('md_order_required', ['0', '1'])->default('0')->comment('0=no,1=yes');
            $table->integer('md_order_due')->nullable();
            $table->string('md_visit_due')->nullable();
            
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
        Schema::dropIfExists('patient_clinical_details');
    }
}
