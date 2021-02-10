<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientAddressesTable extends Migration
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
            $table->foreignId('patient_id')->index('patient_id');
            $table->integer('address_id');
            $table->string('address1');
            $table->string('address2');
            $table->string('cross_street');
            $table->foreignId('city_id')->index('city_id');
            $table->string('zip5');
            $table->string('zip4');
            $table->foreignId('state_id')->index('state_id');
            $table->foreignId('county_id')->index('county_id');
            $table->string('is_primary_address');
            $table->string('addresstypes');

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
