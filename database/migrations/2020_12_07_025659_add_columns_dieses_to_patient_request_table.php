<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsDiesesToPatientRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_requests', function (Blueprint $table) {
            $table->string('dieses')->nullable();
            $table->string('symptoms')->nullable();
            $table->string('is_parking')->nullable();
            $table->enum('status',['active','inactive','complete'])->default('active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_requests', function (Blueprint $table) {
            //
        });
    }
}
