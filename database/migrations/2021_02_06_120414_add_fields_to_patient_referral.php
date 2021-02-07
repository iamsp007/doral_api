<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToPatientReferral extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_referrals', function (Blueprint $table) {
            $table->string('person_code')->nullable();
            $table->string('grp_number')->nullable();
            $table->string('id_number')->nullable();
            $table->string('eff_date')->nullable();
            $table->string('term_date')->nullable();
            $table->string('initial')->nullable();
            $table->string('division')->nullable();
            $table->string('coverage')->nullable();
            $table->string('plan')->nullable();
            $table->string('network')->nullable();
            $table->string('coverage_level')->nullable();
            $table->string('apt')->nullable();
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
            $table->dropColumn('person_code');
            $table->dropColumn('grp_number');
            $table->dropColumn('id_number');
            $table->dropColumn('eff_date');
            $table->dropColumn('term_date');
            $table->dropColumn('initial');
            $table->dropColumn('division');
            $table->dropColumn('coverage');
            $table->dropColumn('plan');
            $table->dropColumn('network');
            $table->dropColumn('coverage_level');
            $table->dropColumn('apt');
        });
    }
}
