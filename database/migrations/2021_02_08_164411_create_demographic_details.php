<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemographicDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demographic_details', function (Blueprint $table) {
            $table->id();
            $table->string('DoralId',6)->unique();
            $table->integer('PatientID')->nullble();
            $table->integer('AgencyID')->nullble();
            $table->integer('OfficeID')->nullble();
            $table->string('FirstName')->nullble();
            $table->string('LastName')->nullble();
            $table->string('BirthDate')->nullble();
            $table->string('Gender')->nullble();
            $table->integer('PriorityCode')->nullble();
            $table->string('ServiceRequestStartDate')->nullble();
            $table->string('AdmissionID')->nullble();
            $table->string('MedicaidNumber')->nullble();
            $table->string('MedicareNumber')->nullble();
            $table->string('SSN')->nullble();
            $table->string('HomePhone')->nullble();
            $table->integer('PayerID')->nullble();
            $table->string('PayerName')->nullble();
            $table->integer('PayerCoordinatorID')->nullble();
            $table->string('PayerCoordinatorName')->nullble();
            $table->integer('PatientStatusID')->nullble();
            $table->string('PatientStatusName')->nullble();
            $table->string('WageParity')->nullble();
            $table->integer('PrimaryLanguageID')->nullble();
            $table->string('PrimaryLanguage')->nullble();
            $table->integer('SecondaryLanguageID')->nullble();
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
        Schema::dropIfExists('demographic_details');
    }
}
