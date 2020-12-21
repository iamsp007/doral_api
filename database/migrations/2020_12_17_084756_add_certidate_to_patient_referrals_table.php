<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCertidateToPatientReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_referrals', function (Blueprint $table) {
            $table->date('cert_start_date')->nullable();
            $table->date('cert_end_date')->nullable();
            $table->date('cert_next_date')->nullable();
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
            $table->dropColumn('cert_start_date');
            $table->dropColumn('cert_end_date');
            $table->dropColumn('cert_next_date');
        });
    }
}
