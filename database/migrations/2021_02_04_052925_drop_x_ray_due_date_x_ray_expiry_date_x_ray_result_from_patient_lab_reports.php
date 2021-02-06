<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropXRayDueDateXRayExpiryDateXRayResultFromPatientLabReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_lab_reports', function (Blueprint $table) {
            $table->dropColumn('x_ray_due_date');
            $table->dropColumn('x_ray_expiry_date');
            $table->dropColumn('x_ray_result');
            $table->dropColumn('type');
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
            $table->date('x_ray_due_date')->after('result')->nullable();
            $table->date('x_ray_expiry_date')->after('x_ray_due_date')->nullable();
            $table->enum('x_ray_result', ['0', '1'])->comment('1=positive,0=negative,')->after('x_ray_expiry_date');
            $table->enum('type', ['0', '1', '2'])->comment('0=normal,1=gold,2=gold plus')->after('expiry_date')->default('0');
        });
    }
}
