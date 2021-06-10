<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlternateBillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alternate_billings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->index('patient_id');
            
            $table->string('is_alternate_billing')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('street')->nullable();
            $table->foreignId('city_id')->index('city_id')->nullable();
            $table->foreignId('state_id')->index('state_id')->nullable();
            $table->string('zip5')->nullable();
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
        Schema::dropIfExists('alternate_billings');
    }
}
