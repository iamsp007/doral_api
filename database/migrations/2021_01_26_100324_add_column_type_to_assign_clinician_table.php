<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnTypeToAssignClinicianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assign_clinician_to_patients', function (Blueprint $table) {
            $table->enum('type',["1",'2','3'])->default('1')->comment('1=case manager,2=primary,3=apecialist physician');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assign_clinician_to_patients', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
