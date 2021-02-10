<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientAddresses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_addresses', function (Blueprint $table) {
            $table->id();
            $table->integer('DoralId');
            $table->integer('AddressID')->nullable();
            $table->string('Address1')->nullable();
            $table->string('Address2')->nullable();
            $table->string('City')->nullable();
            $table->integer('Zip5')->nullable();
            $table->integer('Zip4')->nullable();
            $table->string('State')->nullable();
            $table->string('County')->nullable();
            $table->string('IsPrimaryAddress')->nullable();
            $table->string('AddressTypes')->nullable();
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
        Schema::dropIfExists('patient_addresses');
    }
}
