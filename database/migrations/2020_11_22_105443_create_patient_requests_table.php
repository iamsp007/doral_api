<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('clincial_id')->nullable();
            $table->string('latitude',255)->nullable();
            $table->string('longitude',255)->nullable();
            $table->string('reason',255);
            $table->string('is_active',3)->default('1')->comment('0=Inactive,1=active');
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
        Schema::dropIfExists('patient_requests');
    }
}
