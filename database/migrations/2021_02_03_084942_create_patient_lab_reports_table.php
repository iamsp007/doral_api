<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientLabReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_lab_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_report_type_id')->index('lab_report_type_id');
            $table->foreignId('patient_referral_id')->index('patient_referral_id');
            $table->date('due_date')->nullable();
            $table->date('perform_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->enum('type', ['0', '1', '2'])->comment('0=normal,1=gold,2=gold plus')->default('0');
            $table->enum('result', ['0', '1'])->comment('1=positive,0=negative,');
            $table->date('x_ray_due_date')->nullable();
            $table->date('x_ray_expiry_date')->nullable();
            $table->enum('x_ray_result', ['0', '1'])->comment('1=positive,0=negative,');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patient_lab_reports');
    }
}
