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
            $table->integer('address_id')->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('cross_street')->nullable();
            $table->string('zip5')->nullable();
            $table->string('zip4')->nullable();
            $table->foreignId('city_id')->index('city_id');
            $table->foreignId('state_id')->index('state_id');
            $table->foreignId('county_id')->index('county_id');
            $table->boolean('is_primary_address')->nullable();
            $table->string('address_type')->nullable();

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
