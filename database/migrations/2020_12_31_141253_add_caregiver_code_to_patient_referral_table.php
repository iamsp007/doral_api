<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCaregiverCodeToPatientReferralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_referrals', function (Blueprint $table) {
            $table->string('caregiver_code',50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_referrals', function (Blueprint $table) {
            $table->dropColumn('caregiver_code');
        });
    }
}
