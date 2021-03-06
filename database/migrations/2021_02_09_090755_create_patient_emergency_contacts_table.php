<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientEmergencyContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index('user_id');
            $table->string('name')->nullable();
            $table->string('relation')->nullable();
            $table->string('lives_with_patient')->nullable();
            $table->string('have_keys')->nullable();
            $table->string('phone1')->nullable();
            $table->string('phone2')->nullable();
            $table->string('address')->nullable();
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
        Schema::dropIfExists('patient_emergency_contacts');
    }
}
