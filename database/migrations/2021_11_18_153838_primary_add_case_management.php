<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PrimaryAddCaseManagement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('case_management', function (Blueprint $table) {
            $table->enum('flag', ['1', '2'])->default('2')->comment('1=primary,2=secondary')->after('clinician_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('case_management', function (Blueprint $table) {
            //
        });
    }
}
