<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotePatientLabReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_lab_reports', function (Blueprint $table) {
            $table->text('note')->nullable()->after('result');
            $table->string('titer')->nullable()->after('note');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_lab_reports', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
}
