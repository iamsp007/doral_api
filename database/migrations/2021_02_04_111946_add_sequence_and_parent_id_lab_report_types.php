<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSequenceAndParentIdLabReportTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lab_report_types', function (Blueprint $table) {
            $table->integer('sequence')->after('status');
            $table->bigInteger('parent_id')->after('sequence')->unsigned()->nullable();
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
            $table->dropColumn('sequence');
            $table->dropColumn('parent_id');
        });
    }
}
