<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnManyToPatientReferralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_referrals', function (Blueprint $table) {
            $table->string('work_name')->nullable();
            $table->string('home_phone1')->nullable();
            $table->string('cell_phone1')->nullable();
            $table->string('work_phone3')->nullable();
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
            $table->dropColumn('work_name');
            $table->dropColumn('home_phone1');
            $table->dropColumn('cell_phone1');
            $table->dropColumn('work_phone3');
        });
    }
}
