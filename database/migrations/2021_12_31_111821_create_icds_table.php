<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIcdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('icds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('patient_id')->index('patient_id');
            $table->foreignId('icd_code_id')->index('icd_code_id');
            $table->string('date')->nullable();
            $table->string('date_type')->nullable();
            $table->string('historical_date')->nullable();
            $table->string('identified_during')->nullable();
            $table->enum('primary', ['0','1'])->default('0')->comment('0=secondary,1=primary');
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
        Schema::dropIfExists('icds');
    }
}
