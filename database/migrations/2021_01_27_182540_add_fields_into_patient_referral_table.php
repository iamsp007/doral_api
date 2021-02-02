<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsIntoPatientReferralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_referrals', function (Blueprint $table) {
            $table->string('enrollment')->after('caregiver_code')->nullable();
            $table->date('creation_date')->after('enrollment')->nullable();
            $table->string('services')->after('creation_date')->nullable();
            $table->string('insurance')->after('services')->nullable();
            $table->string('hmo_to_mlts')->after('insurance')->nullable();
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
            $table->dropColumn('enrollment');
            $table->dropColumn('creation_date');
            $table->dropColumn('services');
            $table->dropColumn('insurance');
            $table->dropColumn('hmo_to_mlts');
        });
    }
}
