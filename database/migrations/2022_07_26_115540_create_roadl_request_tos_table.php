<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoadlRequestTosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roadl_request_tos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_request_id')->index('patient_request_id');
            $table->foreignId('clinician_id')->index('clinician_id');
            $table->enum('status', ['0','1'])->default('1')->comment('0=hide,1=show');
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
        Schema::dropIfExists('roadl_request_tos');
    }
}
