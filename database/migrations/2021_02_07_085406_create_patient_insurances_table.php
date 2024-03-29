<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientInsurancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_insurances', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('patient_id');
            $table->string('name');
            $table->string('payer_id');
            $table->string('phone');
            $table->string('policy_no');
            $table->enum('status',['active','inactive'])->default('active');
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
        Schema::create('patient_insurances', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('patient_id');
            $table->dropColumn('name');
            $table->dropColumn('payer_id');
            $table->dropColumn('phone');
            $table->dropColumn('policy_no');
            $table->dropColumn('status');
        });
    }
}
