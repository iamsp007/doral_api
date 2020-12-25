<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientReferralNotSsn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_referral_not_ssn', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_id');
            $table->string('patient_id', 20)->nullable();
            $table->string('caregiver_code', 20)->nullable();
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
        Schema::dropIfExists('patient_referral_not_ssn');
    }
}
