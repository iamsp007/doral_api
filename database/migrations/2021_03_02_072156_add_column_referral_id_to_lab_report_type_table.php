<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnReferralIdToLabReportTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lab_report_types', function (Blueprint $table) {
            $table->bigInteger('referral_id')->after('parent_id')->default('4');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lab_report_types', function (Blueprint $table) {
            $table->dropColumn('referral_id');
        });
    }
}
