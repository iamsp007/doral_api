<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientReferralInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_referral_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->index('patient_id');         
            $table->string('referral_master_id');
            $table->string('referral_created_date');
            $table->string('referral_name');
            $table->string('referral_received_date');
            $table->string('referral_status_id');
            $table->string('referral_status');
            $table->string('referral_commission_status_id');
            $table->string('referral_commission_status');
            $table->string('referral_source_id');
            $table->string('referral_source_name');
            $table->string('referral_source_type');
            $table->string('referral_contact_id');
            $table->string('referral_contact_name');
            $table->string('referral_intake_person_id');
            $table->string('referral_intake_person_name');
            $table->string('referral_account_manager_id');
            $table->string('referral_account_manager_name');
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
        Schema::dropIfExists('patient_referral_infos');
    }
}
