<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEducationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index('user_id');
            $table->string('medical_institute_name');
            $table->longText('medical_institute_address');
            $table->foreignId('medical_institute_state')->index('medical_institute_state');
            $table->foreignId('medical_institute_city')->index('medical_institute_city');
            $table->bigInteger('medical_institute_year_started');
            $table->bigInteger('medical_institute_year_completed');

            $table->string('residency_institute_name');
            $table->longText('residency_institute_address');
            $table->foreignId('residency_institute_state')->index('residency_institute_state');
            $table->foreignId('residency_institute_city')->index('residency_institute_city');
            $table->bigInteger('residency_institute_year_started');
            $table->bigInteger('residency_institute_year_completed');

            $table->string('fellowship_institute_name')->nullable();
            $table->longText('fellowship_institute_address')->nullable();
            $table->foreignId('fellowship_institute_state')->index('fellowship_institute_state')->nullable();
            $table->foreignId('fellowship_institute_city')->index('fellowship_institute_city')->nullable();
            $table->bigInteger('fellowship_institute_year_started')->nullable();
            $table->bigInteger('fellowship_institute_year_completed')->nullable();
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
        Schema::dropIfExists('education');
    }
}
